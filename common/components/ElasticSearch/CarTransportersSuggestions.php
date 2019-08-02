<?php

namespace common\components\ElasticSearch;

use common\models\CarTransporter;
use common\models\CarTransporterCity;
use Yii;
use yii\elasticsearch\Query;

/**
 * Class CarTransportersSuggestions
 *
 * @package common\components\ElasticSearch
 */
class CarTransportersSuggestions
{
    const INDEX = 'car_transporters_suggestions';
    const TYPE = 'car_transporters_suggestion';

    const DEFAULT_POPULARITY = 1;

    /**
     * Returns car transporters suggestions mapping structure
     *
     * @return array
     */
    public static function mapping()
    {
        return [
            'properties' => [
                'user_id' => ['type' => 'integer'],
                'search_radius' => ['type' => 'integer'],
                'available_from' => ['type' => 'integer'],
                'quantity' => ['type' => 'integer'],
                'load_location' => ['type' => 'integer'],
                'unload_location' => ['type' => 'integer'],
                'popularity' => ['type' => 'integer'],
                'last_modified' => ['type' => 'integer'],
                'sent_to_user' => ['type' => 'boolean'],
            ],
        ];
    }

    /**
     * Filters car transporter suggestions
     *
     * @param array $filterData Filtration data
     * @return array
     */
    public static function filter($filterData)
    {
        $query = new Query();
        $results = $query->from(self::INDEX, self::TYPE)
            ->query([
                'bool' => [
                    'must' => self::prepareTerms($filterData),
                ],
            ])
            ->createCommand()
            ->search();

        return $results['hits']['hits'];
    }

    /**
     * Prepares car transporter suggestions filtration terms
     *
     * @param array $filterData Filtration data
     * @return array
     */
    private static function prepareTerms($filterData)
    {
        $terms = [];
        foreach ($filterData as $attribute => $value) {
            array_push($terms, [
                'term' => [
                    $attribute => $value,
                ],
            ]);
        }
        return $terms;
    }

    /**
     * Adds new car transporter suggestion or updates already existing one
     *
     * @param CarTransporter $carTransporter Car transporter model
     * @param CarTransporterCity $carTransporterCity Car transporter city model
     * @return boolean Whether car transporter suggestion was added or updated successfully
     */
    public static function save(CarTransporter $carTransporter, CarTransporterCity $carTransporterCity)
    {
        $suggestion = self::filter([
            'user_id' => Yii::$app->user->id,
            'load_location' => $carTransporterCity->loadLocation,
            'unload_location' => $carTransporterCity->unloadLocation,
        ]);

        if (self::exists($suggestion)) {
            $suggestion = current($suggestion);
            return self::update($carTransporter, $suggestion['_source'], $suggestion['_id']);
        }

        return self::add($carTransporter, $carTransporterCity);
    }

    /**
     * Checks whether car transporter suggestion already exists
     *
     * @param array $suggestion Car transporter suggestion data
     * @return boolean
     */
    private static function exists($suggestion)
    {
        return !empty($suggestion);
    }

    /**
     * Updates existing car transporter suggestion entry
     *
     * @param CarTransporter $carTransporter Car transporter model
     * @param array $suggestion Car transporter suggestion data
     * @param integer $id Car transporter suggestion ID in ElasticSearch
     * @return boolean Whether car transporter suggestion was updated successfully
     */
    private static function update(CarTransporter $carTransporter, $suggestion, $id)
    {
        $suggestion['search_radius'] = $carTransporter->radius;
        $suggestion['available_from'] = empty($carTransporter->available_from) ? 0 : $carTransporter->available_from;
        $suggestion['quantity'] = $carTransporter->quantity;
        $suggestion['popularity'] += 1;
        $suggestion['last_modified'] = time();

        $query = new Query();
        $result = $query->createCommand()->insert(self::INDEX, self::TYPE, $suggestion, $id);
        return self::isUpdatedSuccessfully($result);
    }

    /**
     * Checks whether car transporter suggestion was updated successfully
     *
     * @param array $result Car transporter suggestion update results
     * @return boolean
     */
    private static function isUpdatedSuccessfully($result)
    {
        return !$result['created'] && $result['_shards']['successful'] && $result['_version'] > 1;
    }

    /**
     * Adds new car transporter suggestion entry
     *
     * @param CarTransporter $carTransporter Car transporter model
     * @param CarTransporterCity $carTransporterCity Car transporter city model
     * @return boolean Whether car transporter suggestion was added successfully
     */
    private static function add(CarTransporter $carTransporter, CarTransporterCity $carTransporterCity)
    {
        $suggestion = [
            'user_id' => Yii::$app->user->id,
            'search_radius' => $carTransporter->radius,
            'available_from' => empty($carTransporter->available_from) ? 0 : $carTransporter->available_from,
            'quantity' => $carTransporter->quantity,
            'load_location' => $carTransporterCity->loadLocation,
            'unload_location' => $carTransporterCity->unloadLocation,
            'popularity' => self::DEFAULT_POPULARITY,
            'last_modified' => time(),
        ];

        $query = new Query();
        $result = $query->createCommand()->insert(self::INDEX, self::TYPE, $suggestion);
        return self::isAddedSuccessfully($result);
    }

    /**
     * Checks whether car transporter suggestion was added successfully
     *
     * @param array $result Car transporter suggestion add results
     * @return boolean
     */
    private static function isAddedSuccessfully($result)
    {
        return $result['created'] && $result['_shards']['successful'] && $result['_version'] === 1;
    }
}