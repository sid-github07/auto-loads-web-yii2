<?php

namespace backend\controllers\migration;


use common\components\ElasticSearch\CarTransporters;
use common\components\ElasticSearch\Cities;
use common\models\CarTransporter;
use common\models\CarTransporterCity;
use common\models\City;
use common\models\Company;
use Yii;

class CarTransporterController extends MigrateController
{
    public function actionCarTransporter()
    {
        $autoveziai = Yii::$app->db_prod->createCommand("SELECT * FROM autoveziai LIMIT 1")->queryAll();
        foreach ($autoveziai as $autovezis) {
            if (CarTransporter::find()->where(['id' => $autovezis['id']])->exists()) {
                continue; // Car transporter has already been migrated
            }

            Yii::$app->db->beginTransaction();

            $carTransporter = new CarTransporter(['scenario' => CarTransporter::SCENARIO_SYSTEM_MIGRATES_CAR_TRANSPORTER]);

            // ID
            $carTransporter->id = $autovezis['id'];
            $carTransporter->code = CarTransporter::CODE_DEFAULT_VALUE; // After saving will be assigned
            $carTransporter->validate(['id', 'code']);
            if ($carTransporter->errors) {
                var_dump($carTransporter->id);
                var_dump($carTransporter->errors);
            }

            // User_id
            if (empty($autovezis['vartotojas'])) {
                $company = Company::findOne($autovezis['imone']);
                if (is_null($company)) {
                    $this->writeToCSV(CarTransporter::tableName(), 'Autovežio, kurio ID: ' . $carTransporter->id . ' įmonė neperkelta', $carTransporter->id);
                    Yii::$app->db->transaction->rollBack();
                    continue;
                } else {
                    $carTransporter->user_id = $company->owner_id;
                }
            } else {
                $carTransporter->user_id = $autovezis['vartotojas'];
            }

            $carTransporter->validate(['user_id']);
            if ($carTransporter->errors) {
                $this->writeToCSV(CarTransporter::tableName(), 'Autovežio, kurio ID: ' . $carTransporter->id . ' vartotojas neperkeltas', $carTransporter->id);
                Yii::$app->db->transaction->rollBack();
                continue;
            }

            // Quantity
            if ($autovezis['quantity_to'] < CarTransporter::QUANTITY_MIN_VALUE || $autovezis['quantity_to'] > CarTransporter::QUANTITY_MAX_VALUE) {
                $carTransporter->quantity = CarTransporter::QUANTITY_DEFAULT_VALUE;
            } else {
                $carTransporter->quantity = $autovezis['quantity_to'];
            }
            $carTransporter->validate(['quantity']);
            if ($carTransporter->errors) {
                var_dump($carTransporter->id);
                var_dump($carTransporter->errors);
            }

            // Available_from
            if (is_null($autovezis['planuojama_data']) || empty($autovezis['planuojama_data']) || $autovezis['planuojama_data'] == '0000-00-00') {
                $carTransporter->available_from = CarTransporter::AVAILABLE_FROM_DEFAULT_VALUE;
            } else {
                $carTransporter->available_from = strtotime($autovezis['planuojama_data']);
            }
            $carTransporter->validate(['available_from']);
            if ($carTransporter->errors) {
                var_dump($carTransporter->id);
                var_dump($carTransporter->errors);
            }

            // Date_of_expiry
            $carTransporter->date_of_expiry = strtotime($autovezis['galioja_iki']);
            $carTransporter->validate(['date_of_expiry']);
            if ($carTransporter->errors) {
                var_dump($carTransporter->id);
                var_dump($carTransporter->errors);
            }

            // Visible, archived
            if ($autovezis['galioja_iki'] < date('Y-m-d H:i:s')) {
                if (empty($autovezis['rodomas'])) {
                    $carTransporter->visible = CarTransporter::INVISIBLE;
                    $carTransporter->archived = CarTransporter::ARCHIVED;
                } else {
                    $carTransporter->visible = CarTransporter::INVISIBLE;
                    $carTransporter->archived = CarTransporter::NOT_ARCHIVED;
                }
            } else {
                if (empty($autovezis['rodomas'])) {
                    $carTransporter->visible = CarTransporter::INVISIBLE;
                    $carTransporter->archived = CarTransporter::NOT_ARCHIVED;
                } else {
                    $carTransporter->visible = CarTransporter::VISIBLE;
                    $carTransporter->archived = CarTransporter::NOT_ARCHIVED;
                }
            }
            $carTransporter->validate(['visible', 'archived']);
            if ($carTransporter->errors) {
                var_dump($carTransporter->id);
                var_dump($carTransporter->errors);
            }

            // Created_at, updated_at
            $carTransporter->created_at = strtotime($autovezis['ideta']);
            $carTransporter->updated_at = strtotime($autovezis['atnaujinta']);
            $carTransporter->validate(['created_at', 'updated_at']);
            if ($carTransporter->errors) {
                var_dump($carTransporter->id);
                var_dump($carTransporter->errors);
            }

            $carTransporter->detachBehaviors();
            $carTransporter->save();

            // Car transporter locations migration
            if (empty($autovezis['pasikrovimo_miestas']) && empty($autovezis['issikrovimo_miestas'])) {
                $autoveziuVietos = Yii::$app->db_prod->createCommand("SELECT * FROM autoveziu_vietos WHERE autovezio_id = :id", [':id' => $carTransporter->id])->queryAll();
                foreach ($autoveziuVietos as $vieta) {
                    $carTransporterCity = new CarTransporterCity(['scenario' => CarTransporterCity::SCENARIO_SYSTEM_MIGRATES_CAR_TRANSPORTER_CITY]);
                    $carTransporterCity->car_transporter_id = $carTransporter->id;
                    if (empty($vieta['miesto_id'])) {
                        $country = City::findCountry($vieta['salies_kodas']);
                        $carTransporterCity->city_id = $country->id;
                        $carTransporterCity->country_code = $country->country_code;
                    } else {
                        $carTransporterCity->city_id = $vieta['miesto_id'];
                        $carTransporterCity->country_code = $vieta['salies_kodas'];
                    }
                    $carTransporterCity->type = $vieta['ar_pasikrovimo'] ? CarTransporterCity::TYPE_LOAD : CarTransporterCity::TYPE_UNLOAD;
                    $carTransporterCity->created_at = $carTransporter->created_at;
                    $carTransporterCity->updated_at = $carTransporter->updated_at;
                    $carTransporterCity->validate();
                    if ($carTransporterCity->errors) {
                        var_dump($carTransporterCity->id);
                        var_dump($carTransporterCity->errors);
                    }

                    $carTransporterCity->detachBehaviors();
                    $carTransporterCity->save();
                }
            } else {
                // Loading city
                $carTransporterCity = new CarTransporterCity(['scenario' => CarTransporterCity::SCENARIO_SYSTEM_MIGRATES_CAR_TRANSPORTER_CITY]);
                $carTransporterCity->car_transporter_id = $carTransporter->id;
                $cities = Cities::getSimpleSearchCities($autovezis['pasikrovimo_miestas']);
                if (!empty($cities)) {
                    $carTransporterCity->city_id = $cities[0]['id'];
                    $carTransporterCity->country_code = $cities[0]['country_code'];
                } else {
                    $this->writeToCSV(CarTransporterCity::tableName(), 'Nepavyko rasti miesto su pavadinimu "' . $autovezis['pasikrovimo_miestas'] . '"', $carTransporter->id);
                    Yii::$app->db->transaction->rollBack();
                    continue;
                }
                $carTransporterCity->type = CarTransporterCity::TYPE_LOAD;
                $carTransporterCity->created_at = $carTransporter->created_at;
                $carTransporterCity->updated_at = $carTransporter->updated_at;
                $carTransporterCity->validate();
                if ($carTransporterCity->errors) {
                    var_dump($carTransporter->id);
                    var_dump($carTransporterCity->errors);
                }

                $carTransporterCity->detachBehaviors();
                $carTransporterCity->save();

                // Unloading city
                $carTransporterCity = new CarTransporterCity(['scenario' => CarTransporterCity::SCENARIO_SYSTEM_MIGRATES_CAR_TRANSPORTER_CITY]);
                $carTransporterCity->car_transporter_id = $carTransporter->id;
                $cities = Cities::getSimpleSearchCities($autovezis['issikrovimo_miestas']);
                if (!empty($cities)) {
                    $carTransporterCity->city_id = $cities[0]['id'];
                    $carTransporterCity->country_code = $cities[0]['country_code'];
                } else {
                    $this->writeToCSV(CarTransporterCity::tableName(), 'Nepavyko rasti miesto su pavadinimu "' . $autovezis['issikrovimo_miestas'] . '"', $carTransporter->id);
                    Yii::$app->db->transaction->rollBack();
                    continue;
                }
                $carTransporterCity->type = CarTransporterCity::TYPE_UNLOAD;
                $carTransporterCity->created_at = $carTransporter->created_at;
                $carTransporterCity->updated_at = $carTransporter->updated_at;
                $carTransporterCity->validate();
                if ($carTransporterCity->errors) {
                    var_dump($carTransporter->id);
                    var_dump($carTransporterCity->errors);
                }

                $carTransporterCity->detachBehaviors();
                $carTransporterCity->save();
            }

            // Update ElasticSearch
            $loadCitiesIds = CarTransporterCity::find()
                ->select('city_id')
                ->where([
                    'car_transporter_id' => $carTransporter->id,
                    'type' => CarTransporterCity::TYPE_LOAD,
                ])
                ->column();
            $unloadCitiesIds = CarTransporterCity::find()
                ->select('city_id')
                ->where([
                    'car_transporter_id' => $carTransporter->id,
                    'type' => CarTransporterCity::TYPE_UNLOAD,
                ])
                ->column();

            // Update popular cities and directions
            foreach ($loadCitiesIds as $loadCityId) {
                Cities::updatePopularity($loadCityId, $unloadCitiesIds);
            }

            // Update popular cities
            foreach ($unloadCitiesIds as $unloadCityId) {
                Cities::updatePopularity($unloadCityId);
            }

            $loadLocations = City::findAll($loadCitiesIds);
            $unloadLocations = City::findAll($unloadCitiesIds);
            CarTransporters::add($carTransporter, $loadLocations, $unloadLocations);

            Yii::$app->db->transaction->commit();
        }
    }
}
