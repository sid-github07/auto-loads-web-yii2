<?php

namespace backend\controllers\migration;

use common\components\ElasticSearch\Cities;
use common\components\ElasticSearch\Loads;
use common\models\City;
use Yii;
use yii\web\Controller;

/**
 * Class MigrateController
 *
 * This controller is responsible for common migration actions, like writing error messages to CSV file and so on.
 *
 * @package backend\controllers\migration
 */
class MigrateController extends Controller
{
    const EXTENSION = 'csv';

    /**
     * Writes error messages to CSV file
     *
     * This method writes model errors to corresponding CSV file, when during migration process some errors occurs.
     * CSV file name is formed from model table name.
     *
     * @param string $tableName Object table name
     * @param array|string $errors List of errors if it is array or specific error message if it is string
     * @param null|integer $id Object ID
     */
    protected function writeToCSV($tableName, $errors, $id = null)
    {
        if (is_array($errors)) {
            $errors = json_encode($errors);
        }

        $tableName = $this->formatTableName($tableName);
        $extension = self::EXTENSION;
        $fileName = "$tableName.$extension";
        $file = fopen($fileName, 'a'); // NOTE: "a" stands for "append". The pointer is placed at the end of the file.

        $data = [$tableName, $id, $errors];

        fputcsv($file, $data);
        fclose($file);
    }

    /**
     * Formats table name
     *
     * This method removes curly brackets from presented table name.
     * It also removes percentage mark from beginning of table name.
     * This way we get clear table name, that exist in database.
     *
     * @param string $tableName Unformatted table name
     * @return string
     */
    private function formatTableName($tableName)
    {
        $fileName = ltrim($tableName, '{{%');
        $fileName = rtrim($fileName, '}}');
        return $fileName;
    }

    /**
     * Migrates countries from old system to new one
     */
    public function actionCountries()
    {
        $salys = Yii::$app->db_prod->createCommand("SELECT * FROM salys")->queryAll();
        foreach ($salys as $salis) {
            $city = new City([
                'name' => $salis['pavadinimas_lt'],
                'ansi_name' => $salis['pavadinimas_lt'],
                'alt_name' => $this->formatCountryAltName($salis),
                'latitude' => $salis['latitude'],
                'longitude' => $salis['longitude'],
                'country_code' => strtoupper($salis['kodas']),
                'population' => null,
                'elevation' => null,
                'timezone' => null,
                'modification_date' => null,
            ]);
            $city->save();
        }

        Cities::addCountries();
    }

    /**
     * Formats country alternative name from old system to new one
     *
     * @param array $salis Information about the country
     * @return string
     */
    private function formatCountryAltName($salis)
    {
        $en = $salis['pavadinimas_en'];
        $de = $salis['pavadinimas_de'];
        $fr = $salis['pavadinimas_fr'];
        $es = $salis['pavadinimas_es'];
        $pl = $salis['pavadinimas_pl'];
        $it = $salis['pavadinimas_it'];
        $ru = $salis['pavadinimas_ru'];
        return "$en,$de,$fr,$es,$pl,$it,$ru";
    }

    /**
     * Adds country code to ElasticSearch load and unload cities
     */
    public function actionAddCountryCode()
    {
        Loads::addCountryCode();
    }
}