<?php

namespace backend\controllers\migration;

use common\components\ElasticSearch\Cities;
use common\components\ElasticSearch\Loads;
use common\models\City;
use common\models\Company;
use common\models\Load;
use common\models\LoadCar;
use common\models\LoadCity;
use Yii;
use yii\helpers\Html;

/**
 * Class LoadController
 *
 * This controller is responsible for migrating logged-in user and guest loads, loads cities, loads cars
 * and also writes all load information to elastic search
 *
 * @package backend\controllers\migration
 */
class LoadController extends MigrateController
{
    const DEFAULT_PAYMENT_METHOD = Load::FOR_ALL_LOAD;
    const DEFAULT_PHONE = '+37061234567';

    public function actionLoads()
    {
        $kroviniai = Yii::$app->db_prod->createCommand("SELECT * FROM kroviniai LIMIT 1")->queryAll();
        foreach ($kroviniai as $krovinys) {
            if (Load::find()->where(['id' => $krovinys['id']])->exists()) {
                continue; // Load has already been migrated
            }

            if (empty($krovinys['savininkas'])) {
                $this->writeToCSV(Load::tableName(), 'Krovinys, kurio ID: ' . $krovinys['id'] . ' neturi savininko');
                continue;
            }

            Yii::$app->db->beginTransaction();

            // Load migration
            $load = new Load(['scenario' => Load::SCENARIO_SYSTEM_MIGRATES_LOAD]);

            // ID, code
            $load->id = $krovinys['id'];
            $load->code = Load::DEFAULT_CODE; // After saving will be assigned
            $load->validate(['id', 'code']);
            if ($load->errors) {
                var_dump($load->id);
                var_dump($load->errors);
            }

            // Type
            $load->type = $krovinys['dalinis_krovinys'] ? Load::TYPE_PARTIAL : Load::TYPE_FULL;
            $load->validate(['type']);
            if ($load->errors) {
                var_dump($load->id);
                var_dump($load->errors);
            }

            // Payment_method
            if ($krovinys['kaina'] == '0.00') {
                if ($krovinys['vntkaina'] == '0.00') {
                    $load->payment_method = self::DEFAULT_PAYMENT_METHOD;
                } else {
                    $load->payment_method = Load::FOR_CAR_MODEL;
                }
            } else {
                $load->payment_method = Load::FOR_ALL_LOAD;
            }
            $load->validate(['payment_method']);
            if ($load->errors) {
                var_dump($load->id);
                var_dump($load->errors);
            }

            // Date
            $date = $krovinys['pakrovimo_data'];
            $load->date = (is_null($date) || empty($date) || $date === '0000-00-00') ? 0 : strtotime($date);
            $load->validate(['date']);
            if ($load->errors) {
                var_dump($load->id);
                var_dump($load->errors);
            }

            // Price
            if ($krovinys['kaina'] == '0.00') {
                if ($krovinys['vntkaina'] == '0.00') {
                    $load->price = Load::DEFAULT_PRICE;
                } else {
                    $load->price = $krovinys['vntkaina'];
                }
            } else {
                $load->price = $krovinys['kaina'];
            }
            $load->validate(['price']);
            if ($load->errors) {
                var_dump($load->id);
                var_dump($load->errors);
            }

            // Status, active
            if ($krovinys['galioja_iki'] < date('Y-m-d H:i:s')) {
                if (empty($krovinys['rodomas'])) {
                    $load->status = Load::INACTIVE;
                    $load->active = Load::NOT_ACTIVATED;
                } else {
                    $load->status = Load::ACTIVE;
                    $load->active = Load::NOT_ACTIVATED;
                }
            } else {
                if (empty($krovinys['rodomas'])) {
                    $load->status = Load::ACTIVE;
                    $load->active = Load::NOT_ACTIVATED;
                } else {
                    $load->status = Load::ACTIVE;
                    $load->active = Load::ACTIVATED;
                }
            }
            $load->validate(['status', 'active']);
            if ($load->errors) {
                var_dump($load->id);
                var_dump($load->errors);
            }

            // Date_of_expiry
            $load->date_of_expiry = strtotime($krovinys['galioja_iki']);
            $load->validate(['date_of_expiry']);
            if ($load->errors) {
                var_dump($load->id);
                var_dump($load->errors);
            }

            // Created_at, updated_at
            $load->created_at = strtotime($krovinys['ideta']);
            $load->updated_at = strtotime($krovinys['redagavimo_data']);
            $load->validate(['created_at', 'updated_at']);
            if ($load->errors) {
                var_dump($load->id);
                var_dump($load->errors);
            }

            // User_id, token, phone, email
            if (!empty($krovinys['vartotojas'])) {
                $load->user_id = $krovinys['vartotojas'];
                $load->validate(['user_id']);
                if ($load->errors) {
                    $this->writeToCSV(Load::tableName(), 'Krovinio, kurio ID: ' . $load->id . ' vartotojas neegzistuoja');
                    Yii::$app->db->transaction->rollBack();
                    continue;
                }
                $load->token = Load::DEFAULT_TOKEN;
                $load->phone = Load::DEFAULT_PHONE;
                $load->email = Load::DEFAULT_EMAIL;
            } else {
                $imone = Yii::$app->db_prod->createCommand("SELECT * FROM imones WHERE id = :id", [':id' => $krovinys['savininkas']])->queryOne();
                switch ($imone['kategorija']) {
                    case '14':
                    case '16':
                    case '18':
                        // Neregistruotas
                        $load->user_id = Load::DEFAULT_USER_ID;
                        $load->token = Yii::$app->security->generateRandomString(Load::TOKEN_LENGTH);
                        $load->phone = $imone['telefonai'];
                        $load->email = $imone['elpastas'];
                        break;
                    case '6':
                    case '7':
                    case '11':
                    case '12':
                    case '13':
                        // Registruotas
                        $company = Company::findOne($imone['id']);
                        if (is_null($company)) {
                            $load->user_id = Load::DEFAULT_USER_ID;
                            $load->token = Yii::$app->security->generateRandomString(Load::TOKEN_LENGTH);
                            $load->phone = $imone['telefonai'];
                            $load->email = $imone['elpastas'];
                        } else {
                            // naudoti company->user_id
                            $load->user_id = $company->owner_id;
                            $load->token = Load::DEFAULT_TOKEN;
                            $load->phone = Load::DEFAULT_PHONE;
                            $load->email = Load::DEFAULT_EMAIL;
                        }
                        break;
                    default:
                        $load->user_id = Load::DEFAULT_USER_ID;
                        $load->token = Yii::$app->security->generateRandomString(Load::TOKEN_LENGTH);
                        $load->phone = $imone['telefonai'];
                        $load->email = $imone['elpastas'];
                        break;
                }
            }
            $load->validate(['user_id', 'token', 'phone', 'email']);
            if ($load->errors) {
                var_dump($load->id);
                var_dump($load->errors);
            }

            $load->detachBehaviors();
            $load->save();

            // Update load code
            $symbol = Load::LOAD_CODE_SYMBOL;
            $year = date('Y', $load->created_at);
            $month = date('m', $load->created_at);
            $number = sprintf("%'.06d", $load->id);
            $load->code = $symbol . "$year-$month-$number";
            $load->scenario = Load::SCENARIO_UPDATE_CODE;
            $load->detachBehaviors();
            $load->save();

            // Load cities migration
            if (empty($krovinys['pasikrovimo_miestas']) && empty($krovinys['issikrovimo_miestas'])) {
                $kroviniuVietos = Yii::$app->db_prod->createCommand("SELECT * FROM kroviniu_vietos WHERE krovinio_id = :id", [':id' => $load->id])->queryAll();
                foreach ($kroviniuVietos as $vieta) {
                    $loadCity = new LoadCity(['scenario' => LoadCity::SCENARIO_SYSTEM_MIGRATES_LOAD_CITY]);
                    $loadCity->load_id = $load->id;
                    if (empty($vieta['miesto_id'])) {
                        $country = City::findCountry($vieta['salies_kodas']);
                        $loadCity->city_id = $country->id;
                    } else {
                        $loadCity->city_id = $vieta['miesto_id'];
                    }
                    $loadCity->type = $vieta['ar_pasikrovimo'] ? LoadCity::LOADING : LoadCity::UNLOADING;
                    $loadCity->created_at = $load->created_at;
                    $loadCity->updated_at = $load->updated_at;
                    $loadCity->validate();
                    if ($loadCity->errors) {
                        var_dump($load->id);
                        var_dump($loadCity->errors);
                    }

                    $loadCity->detachBehaviors();
                    $loadCity->save();
                }
            } else {
                // Loading city
                $loadCity = new LoadCity(['scenario' => LoadCity::SCENARIO_SYSTEM_MIGRATES_LOAD_CITY]);
                $loadCity->load_id = $load->id;
                $cities = Cities::getSimpleSearchCities($krovinys['pasikrovimo_miestas']);
                if (!empty($cities)) {
                    $loadCity->city_id = $cities[0]['id'];
                } else {
                    $this->writeToCSV(LoadCity::tableName(), 'Nepavyko rasti miesto su pavadinimu "' . $krovinys['pasikrovimo_miestas'] . '"', $load->id);
                    Yii::$app->db->transaction->rollBack();
                    continue;
                }
                $loadCity->type = LoadCity::LOADING;
                $loadCity->created_at = $load->created_at;
                $loadCity->updated_at = $load->updated_at;
                $loadCity->validate();
                if ($loadCity->errors) {
                    var_dump($load->id);
                    var_dump($loadCity->errors);
                }

                $loadCity->detachBehaviors();
                $loadCity->save();

                // Unloading city
                $loadCity = new LoadCity(['scenario' => LoadCity::SCENARIO_SYSTEM_MIGRATES_LOAD_CITY]);
                $loadCity->load_id = $load->id;
                $cities = Cities::getSimpleSearchCities($krovinys['issikrovimo_miestas']);
                if (!empty($cities)) {
                    $loadCity->city_id = $cities[0]['id'];
                } else {
                    $this->writeToCSV(LoadCity::tableName(), 'Nepavyko rasti miesto su pavadinimu "' . $krovinys['issikrovimo_miestas'] . '"', $load->id);
                    Yii::$app->db->transaction->rollBack();
                    continue;
                }
                $loadCity->type = LoadCity::UNLOADING;
                $loadCity->created_at = $load->created_at;
                $loadCity->updated_at = $load->updated_at;
                $loadCity->validate();
                if ($loadCity->errors) {
                    var_dump($load->id);
                    var_dump($loadCity->errors);
                }

                $loadCity->detachBehaviors();
                $loadCity->save();
            }

            // Load cars migration
            $kroviniuKomplektacijos = Yii::$app->db_prod->createCommand("SELECT * FROM kroviniu_komplektacijos WHERE krovinio_id = :id", [':id' => $load->id])->queryAll();
            if (empty($kroviniuKomplektacijos)) {
                // create load car entry from load
                $loadCar = new LoadCar(['scenario' => LoadCar::SCENARIO_SYSTEM_MIGRATES_LOAD_CAR]);
                $loadCar->load_id = $load->id;
                if (!is_numeric($krovinys['kiekis'])) {
                    // skip
                    Yii::$app->db->transaction->commit();
                    $this->updateElasticSearch($load);
                    continue;
                } else {
                    if ($krovinys['kiekis'] < LoadCar::QUANTITY_MIN_VALUE || $krovinys['kiekis'] > LoadCar::QUANTITY_MAX_VALUE) {
                        // skip
                        Yii::$app->db->transaction->commit();
                        $this->updateElasticSearch($load);
                        continue;
                    } else {
                        $loadCar->quantity = $krovinys['kiekis'];
                    }
                }
                $loadCar->model = LoadCar::DEFAULT_MODEL;
                $loadCar->price = $load->price;
                switch ($krovinys['auto_bukle']) {
                    case 1:
                        $loadCar->state = LoadCar::USED_CAR;
                        break;
                    case 2:
                        $loadCar->state = LoadCar::NEW_CAR;
                        break;
                    case 3:
                        $loadCar->state = LoadCar::NOT_DRIVING_CAR;
                        break;
                    default:
                        $loadCar->state = LoadCar::DEFAULT_STATE;
                        break;
                }
                $loadCar->created_at = $load->created_at;
                $loadCar->updated_at = $load->updated_at;
                $loadCar->validate();
                if ($loadCar->errors) {
                    var_dump($load->id);
                    var_dump($loadCar->errors);
                }

                $loadCar->detachBehaviors();
                $loadCar->save();
            } else {
                // create load car entry from kroviniu_komplektacijos
                foreach ($kroviniuKomplektacijos as $komplektacija) {
                    $loadCar = new LoadCar(['scenario' => LoadCar::SCENARIO_SYSTEM_MIGRATES_LOAD_CAR]);
                    $loadCar->load_id = $load->id;
                    if ($komplektacija['kiekis'] < LoadCar::QUANTITY_MIN_VALUE || $komplektacija['kiekis'] > LoadCar::QUANTITY_MAX_VALUE) {
                        continue;
                    }
                    $loadCar->quantity = $komplektacija['kiekis'];
                    $loadCar->model = empty($komplektacija['pavadinimas']) ? LoadCar::DEFAULT_MODEL : substr($komplektacija['pavadinimas'], 0, 15);
                    $loadCar->price = empty($komplektacija['vnt_kaina']) ? LoadCar::DEFAULT_PRICE : $komplektacija['vnt_kaina'];
                    switch ($krovinys['auto_bukle']) {
                        case 1:
                            $loadCar->state = LoadCar::USED_CAR;
                            break;
                        case 2:
                            $loadCar->state = LoadCar::NEW_CAR;
                            break;
                        case 3:
                            $loadCar->state = LoadCar::NOT_DRIVING_CAR;
                            break;
                        default:
                            $loadCar->state = LoadCar::DEFAULT_STATE;
                            break;
                    }
                    $loadCar->created_at = $load->created_at;
                    $loadCar->updated_at = $load->updated_at;

                    $loadCar->validate();
                    if ($loadCar->errors) {
                        var_dump($load->id);
                        var_dump($loadCar->errors);
                    }

                    $loadCar->detachBehaviors();
                    $loadCar->save();
                }
            }

            Yii::$app->db->transaction->commit();
            $this->updateElasticSearch($load);
        }
    }

    /**
     * Migrates loads from old system to new one
     */
    public function actionLoad()
    {
        $kroviniai = Yii::$app->db_prod->createCommand("SELECT * FROM kroviniai LIMIT 1")->queryAll();
        foreach ($kroviniai as $krovinys) {
            if ($this->loadExists($krovinys['id'])) {
                continue;
            }

            if ($this->loadHasOwner($krovinys)) {
                $this->writeToCSV(Load::tableName(), 'Krovinys neturi savininko', $krovinys['id']);
                continue;
            }

            $query = "SELECT * FROM imones WHERE id = :id";
            $imone = Yii::$app->db_prod->createCommand($query, [':id' => $krovinys['savininkas']])->queryOne();

            Yii::$app->db->beginTransaction();

            $load = $this->migrateLoad($krovinys, $imone);
            if (is_null($load)) {
                Yii::$app->db->transaction->rollBack();
                continue;
            }

            if (!$this->migrateLoadCities($load, $krovinys)) {
                Yii::$app->db->transaction->rollBack();
                continue;
            }

            if (!$this->migrateLoadCars($load, $krovinys)) {
                Yii::$app->db->transaction->rollBack();
                continue;
            }

            $this->updateElasticSearch($load);

            Yii::$app->db->transaction->commit();
        }
    }

    /**
     * Checks whether load has owner
     *
     * @param array $krovinys Information about the load
     * @return boolean
     */
    private function loadHasOwner($krovinys)
    {
        return empty($krovinys['savininkas']);
    }

    /**
     * Checks whether load has been already migrated
     *
     * @param null|integer $id Load ID
     * @return boolean
     */
    private function loadExists($id)
    {
        return Load::find()->where(compact('id'))->exists();
    }

    /**
     * Migrates load from old system to new one
     *
     * @param array $krovinys Information about the load
     * @param array $imone Information about the company
     * @return Load|null
     */
    private function migrateLoad($krovinys, $imone)
    {
        // TODO: iškelti iš funkcijos į parent funkciją
        $isGuest = $this->loadIsAnnouncedByGuest($krovinys);
        $isUnregistered = $this->isCompanyUnregistered($imone);
        if ($isGuest && !$isUnregistered) {
            $message = 'Loginė klaida: įmonė registruota, tačiau krovinys neturi vartotojo';
            $this->writeToCSV(Load::tableName(), $message, $krovinys['id']);
            return null;
        }

        $load = new Load([
            'scenario' => Load::SCENARIO_SYSTEM_MIGRATES_LOAD_DATA,
            'id' => $krovinys['id'],
            'user_id' => $this->convertUserId($krovinys, $isGuest),
            'code' => Load::DEFAULT_CODE,
            'type' => $this->convertType($krovinys),
            'payment_method' => $this->convertPaymentMethod($krovinys),
            'date' => $this->convertDate($krovinys),
            'price' => $krovinys['kaina'],
            'status' => $this->convertStatus($krovinys),
            'active' => $this->convertActive($krovinys),
            'date_of_expiry' => strtotime($krovinys['galioja_iki']),
            'token' => $this->convertToken($isGuest),
            'phone' => $this->convertPhone($imone, $isGuest),
            'email' => $this->convertEmail($imone, $isGuest),
            'created_at' => strtotime($krovinys['ideta']),
            'updated_at' => strtotime($krovinys['ideta']),
        ]);

        if ($load->hasInvalidDate()) {
            $this->writeToCSV(Load::tableName(), 'Neteisinga krovinio pakrovimo data', $load->id);
            return null;
        }

        $this->fixPhone($load);

        $load->validate();
        if ($load->errors) {
            $this->writeToCSV(Load::tableName(), $load->errors, $load->id);
            return null;
        }

        $load->detachBehaviors();
        $load->save(false);
        return $this->updateLoadCode($load);
    }

    /**
     * Checks whether load is announced by guest
     *
     * @param array $krovinys Information about the load
     * @return boolean
     */
    private function loadIsAnnouncedByGuest($krovinys)
    {
        return empty($krovinys['vartotojas']);
    }

    /**
     * Checks whether company is unregistered
     *
     * @todo refactor method name
     * @param array $imone Information about the company which announced the load
     * @return boolean
     */
    private function isCompanyUnregistered($imone)
    {
        return is_null($imone['kategorija']) || $imone['kategorija'] == 14; // Neregistruota
    }

    /**
     * Converts load owner ID from old system to new one
     *
     * @param array $krovinys Information about the load
     * @param boolean $isGuest Attribute, whether load was announced by guest
     * @return null|integer
     */
    private function convertUserId($krovinys, $isGuest)
    {
        return $isGuest ? Load::DEFAULT_USER_ID : $krovinys['vartotojas'];
    }

    /**
     * Converts load type from old system to new one
     *
     * @param array $krovinys Information about the load
     * @return integer
     */
    private function convertType($krovinys)
    {
        return $krovinys['dalinis_krovinys'] ? Load::TYPE_PARTIAL : Load::TYPE_FULL;
    }

    /**
     * Converts load payment method from old system to new one
     *
     * @param array $krovinys Information about the load
     * @return integer
     */
    private function convertPaymentMethod($krovinys)
    {
        if (!empty($krovinys['apmokejimas'])) {
            return self::DEFAULT_PAYMENT_METHOD;
        }

        if (!empty($krovinys['kaina']) && empty($krovinys['vntkaina'])) {
            return Load::FOR_ALL_LOAD;
        }

        if (empty($krovinys['kaina']) && !empty($krovinys['vntkaina'])) {
            return Load::FOR_CAR_MODEL;
        }

        return self::DEFAULT_PAYMENT_METHOD;
    }

    /**
     * Converts load date from old system to new one
     *
     * @param array $krovinys Information about the load
     * @return false|integer
     */
    private function convertDate($krovinys)
    {
        $date = $krovinys['pakrovimo_data'];
        return is_null($date) || empty($date) || $date === '0000-00-00' ? 0 : strtotime($date);
    }

    /**
     * Converts load status from old system to new one
     *
     * @param array $krovinys Information about the load
     * @return integer
     */
    private function convertStatus($krovinys)
    {
        return $krovinys['rodomas'] ? Load::ACTIVE : Load::INACTIVE;
    }

    /**
     * Converts load activity status from old system to new one
     *
     * @param array $krovinys Information about the load
     * @return integer
     */
    private function convertActive($krovinys)
    {
        return $krovinys['rodomas_puslapyje'] ? Load::ACTIVATED : Load::NOT_ACTIVATED;
    }

    /**
     * Converts load token from old system to new one
     *
     * @param boolean $isGuest Attribute, whether load was announced by guest
     * @param integer $length Token length
     * @return null|string
     */
    private function convertToken($isGuest, $length = Load::TOKEN_LENGTH)
    {
        if ($isGuest) {
            return Yii::$app->security->generateRandomString($length);
        }

        return Load::DEFAULT_TOKEN;
    }

    /**
     * Converts load owner company phone number from old system to new one
     *
     * @param array $imone Information about the company which announced the load
     * @param boolean $isGuest Attribute, whether load was announced by guest
     * @return null|string
     */
    private function convertPhone($imone, $isGuest)
    {
        return $isGuest ? $imone['telefonai'] : Load::DEFAULT_PHONE;
    }

    /**
     * Converts load owner company email from old system to new one
     *
     * @param array $imone Information about the company which announced the load
     * @param boolean $isGuest Attribute, whether load was announced by guest
     * @return null|string
     */
    private function convertEmail($imone, $isGuest)
    {
        return $isGuest ? $imone['elpastas'] : Load::DEFAULT_EMAIL;
    }

    /**
     * Fixes load phone number
     *
     * @param Load $load Load model
     */
    private function fixPhone(Load &$load)
    {
        $load->phone = trim(str_replace(' ', '', $load->phone));
        if (!$load->validate(['phone'])) {
            $load->phone = self::DEFAULT_PHONE;
        }

        return;
    }

    /**
     * Updates load code
     *
     * @param Load $load Load model
     * @return Load
     */
    private function updateLoadCode(Load $load)
    {
        $symbol = Load::LOAD_CODE_SYMBOL;
        $year = date('Y', $load->created_at);
        $month = date('m', $load->created_at);
        $number = sprintf("%'.06d", $load->id);
        $load->code = $symbol . "$year-$month-$number";
        $load->scenario = Load::SCENARIO_UPDATE_CODE;
        $load->detachBehaviors(); // Remove timestamp behaviour
        $load->save();
        return $load;
    }

    /**
     * Migrates load cities from old system to new one
     *
     * In old system load cities are stored in two different tables.
     * Some cities are stored in the same table as loads,
     * and other cities are stored in different table.
     *
     * @param Load $load Load model
     * @param array $krovinys Information about the load
     * @return boolean|null
     */
    private function migrateLoadCities(Load $load, $krovinys)
    {
        if ($this->loadCitiesAreInKroviniai($krovinys)) {
            return $this->migrateLoadCitiesFromKroviniai($load, $krovinys);
        }

        return $this->migrateLoadCitiesFromKroviniuVietos($load);
    }

    /**
     * Checks whether load cities are in the same database table as information about the load
     *
     * @param array $krovinys Information about the load
     * @return boolean
     */
    private function loadCitiesAreInKroviniai($krovinys)
    {
        return !empty($krovinys['pasikrovimo_miestas']) && !empty($krovinys['issikrovimo_miestas']);
    }

    /**
     * Migrates load cities from old system to new one when load cities are stored in the same table as load information
     *
     * @param Load $load Load model
     * @param array $krovinys Information about the load
     * @return boolean|null
     */
    private function migrateLoadCitiesFromKroviniai(Load $load, $krovinys)
    {
        $loadCityId = $this->findCityIdByName($krovinys['pasikrovimo_miestas']);
        $unloadCityId = $this->findCityIdByName($krovinys['issikrovimo_miestas']);

        if (is_null($loadCityId)) {
            $message = 'Nepavyko rasti miesto su pavadinimu "' . $krovinys['pasikrovimo_miestas'] . '"';
            $this->writeToCSV(LoadCity::tableName(), $message, $load->id);
            return null;
        }

        if (is_null($unloadCityId)) {
            $message = 'Nepavyko rasti miesto su pavadinimu "' . $krovinys['issikrovimo_miestas'] . '"';
            $this->writeToCSV(LoadCity::tableName(), $message, $load->id);
            return null;
        }

        $loadCities = [
            LoadCity::LOADING => $loadCityId,
            LoadCity::UNLOADING => $unloadCityId,
        ];

        foreach ($loadCities as $type => $cityId) {
            if (!$this->migrateLoadCity($load, $cityId, $type)) {
                return null;
            }
        }

        return true;
    }

    /**
     * Finds and returns city ID by presented city name
     *
     * @param string $name City name
     * @return null|integer
     */
    private function findCityIdByName($name)
    {
        $search = Cities::simpleSearch($name);
        if (empty($search)) {
            return null;
        }

        $city = current($search);
        return $city['_id'];
    }

    /**
     * Migrates load cities from old system to new one when load cities are stored in different tables than load info
     *
     * @param Load $load Load model
     * @return boolean|null
     */
    private function migrateLoadCitiesFromKroviniuVietos(Load $load)
    {
        $query = "SELECT * FROM kroviniu_vietos WHERE krovinio_id = :id";
        $kroviniuVietos = Yii::$app->db_prod->createCommand($query, [':id' => $load->id])->queryAll();
        foreach ($kroviniuVietos as $vieta) {
            if (empty($vieta['miesto_id'])) {
                $this->writeToCSV(LoadCity::tableName(), 'Krovinio miestas yra tuščias', $load->id);
                return null;
            }

            if (!$this->migrateLoadCity($load, $vieta['miesto_id'], $this->convertLoadCityType($vieta))) {
                return null;
            }
        }

        return true;
    }

    /**
     * Converts load city type from old system to new one
     *
     * @param array $vieta Information about load city
     * @return integer
     */
    private function convertLoadCityType($vieta)
    {
        return $vieta['ar_pasikrovimo'] ? LoadCity::LOADING : LoadCity::UNLOADING;
    }

    /**
     * Migrates load city from old system to new one
     *
     * @param Load $load Load model
     * @param integer $cityId Load city ID
     * @param integer $type Load city type (loading or unloading)
     * @return boolean|null
     */
    private function migrateLoadCity(Load $load, $cityId, $type)
    {
        $loadCity = new LoadCity([
            'scenario' => LoadCity::SCENARIO_SYSTEM_MIGRATES_LOAD_CITIES_DATA,
            'load_id' => $load->id,
            'city_id' => $cityId,
            'type' => $type,
            'created_at' => $load->created_at,
            'updated_at' => $load->updated_at,
        ]);

        $loadCity->validate();
        if ($loadCity->errors) {
            $this->writeToCSV(LoadCity::tableName(), $loadCity->errors, $load->id);
            return null;
        }

        $loadCity->detachBehaviors(); // Remove timestamp behaviors
        return $loadCity->save(false);
    }

    /**
     * Migrates load cars from old system to new one
     *
     * @param Load $load Load model
     * @param array $krovinys Information about the load
     * @return boolean|null
     */
    private function migrateLoadCars(Load $load, $krovinys)
    {
        $query = "SELECT * FROM kroviniu_komplektacijos WHERE krovinio_id = :id";
        $kroviniuKomplektacijos = Yii::$app->db_prod->createCommand($query, [':id' => $load->id])->queryAll();
        foreach ($kroviniuKomplektacijos as $komplektacija) {
            if (!$this->migrateLoadCar($load, $komplektacija, $krovinys)) {
                return null;
            }
        }

        return true;
    }

    /**
     * Migrates load car from old system to new one
     *
     * @param Load $load Load model
     * @param array $komplektacija Information about the load car
     * @param array $krovinys Information about the load
     * @return boolean|null
     */
    private function migrateLoadCar(Load $load, $komplektacija, $krovinys)
    {
        $loadCar = new LoadCar([
            'scenario' => LoadCar::SCENARIO_SYSTEM_MIGRATES_LOAD_CAR_DATA,
            'id' => $komplektacija['id'],
            'load_id' => $load->id,
            'quantity' => $komplektacija['kiekis'],
            'model' => $this->convertLoadCarModel($komplektacija['pavadinimas']),
            'price' => $komplektacija['vnt_kaina'],
            'state' => $this->convertLoadCarState($krovinys['auto_bukle']),
            'created_at' => $load->created_at,
            'updated_at' => $load->updated_at,
        ]);

        if ($loadCar->exceededQuantity()) {
            $this->writeToCSV(LoadCar::tableName(), 'Viršytas automobilių kiekis', $loadCar->id);
            return null;
        }

        $loadCar->validate();
        if ($loadCar->errors) {
            $this->writeToCSV(LoadCar::tableName(), $loadCar->errors, $loadCar->id);
            return null;
        }

        $loadCar->detachBehaviors(); // Remove timestamp behavior
        return $loadCar->save(false);
    }

    /**
     * Converts load car model name from old system to new one
     *
     * @param string $model Old load car model name
     * @return string
     */
    private function convertLoadCarModel($model)
    {
        $model = Html::encode($model);
        $model = substr($model, 0, LoadCar::MODEL_MAX_LENGTH); // 0 - start from the beginning
        return utf8_encode($model);
    }

    /**
     * Converts load car state from old system to new one
     *
     * @param integer $state Old load car state
     * @return integer|null
     */
    private function convertLoadCarState($state)
    {
        switch ($state) {
            case 1:
                return LoadCar::USED_CAR;
            case 2:
                return LoadCar::NEW_CAR;
            case 3:
                return LoadCar::NOT_DRIVING_CAR;
            default:
                return LoadCar::DEFAULT_STATE;
        }
    }

    /**
     * Updates elastic search popular cities and adds load information to elastic search
     *
     * @param Load $load Load model
     */
    private function updateElasticSearch(Load $load)
    {
        $loadCitiesIds = $this->findLoadCityId($load->id, LoadCity::LOADING);
        $unloadCitiesIds = $this->findLoadCityId($load->id, LoadCity::UNLOADING);

        // Updates popular cities and directions
        foreach ($loadCitiesIds as $loadCitiesId) {
            Cities::updatePopularity($loadCitiesId, $unloadCitiesIds);
        }

        // Updates popular cities
        foreach ($unloadCitiesIds as $unloadCitiesId) {
            Cities::updatePopularity($unloadCitiesId);
        }

        $this->addLoadToElasticSearch($load, $loadCitiesIds, $unloadCitiesIds);
    }

    /**
     * Finds and returns load city ID
     *
     * @param integer $load_id Load ID
     * @param integer $type Load city type
     * @return array
     */
    private function findLoadCityId($load_id, $type)
    {
        return LoadCity::find()->select('city_id')->where(compact('load_id', 'type'))->column();
    }

    /**
     * Adds load information to elastic search
     *
     * @param Load $load Load model
     * @param array $loadCitiesIds List of load cities that type is load
     * @param array $unloadCitiesIds List of load cities that type is unload
     */
    private function addLoadToElasticSearch(Load $load, $loadCitiesIds, $unloadCitiesIds)
    {
        $carsQuantity = LoadCar::find()->where(['load_id' => $load->id])->sum('quantity');
        if (is_null($carsQuantity)) {
            $carsQuantity = 0;
        }

        $load->date = $load->date ? $load->date : 0;
        Loads::addLoad($load, $carsQuantity, $loadCitiesIds, $unloadCitiesIds);
    }
}
