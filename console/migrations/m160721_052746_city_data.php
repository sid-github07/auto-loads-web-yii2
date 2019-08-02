<?php

use common\models\City;
use yii\db\Migration;

/**
 * Class m160721_052746_city_data
 */
class m160721_052746_city_data extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        for ($i = 1; $i <= Yii::$app->params['numberOfCityFiles']; $i++) {
            $citySource = Yii::$app->params['pathToCityFiles'] . 'city' . $i . '.csv';
            $cityCSV = file($citySource);
            $cityData = [];

            foreach ($cityCSV as $line) {
                $cityData[] = str_getcsv($line);
            }

            $chunks = $this->getArrayChunks($cityData, 1000);
            foreach ($chunks as $chunk) {
                $this->batchInsert(City::tableName(), [
                    'id',
                    'name',
                    'ansi_name',
                    'alt_name',
                    'latitude',
                    'longitude',
                    'country_code',
                    'population',
                    'elevation',
                    'timezone',
                    'modification_date',
                ], $chunk);
            }
        }
        
        $citySource = Yii::$app->params['pathToCityFiles'] . 'countries_to_city_table.csv';
        $cityCSV = file($citySource);
        $cityData = [];
    
        foreach ($cityCSV as $line) {
            $cityData[] = str_getcsv($line);
        }
        
        $this->batchInsert(City::tableName(), [
            'id',
            'name',
            'ansi_name',
            'alt_name',
            'latitude',
            'longitude',
            'country_code',
            'population',
            'elevation',
            'timezone',
            'modification_date',
        ], $cityData);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->truncateTable(City::tableName());
    }
    
    /**
     * Splits array to smaller chunk arrays by specified size
     * 
     * @param array $source
     * @param integer $size
     * @return array
     */
    private function getArrayChunks($source, $size)
    {
        $result = [];
        
        while(!empty($source)) {
            $length = count($source) > $size ? $size : count($source);
            $result[] = array_splice($source, 0, $length);
        }

        return $result;
    }
}