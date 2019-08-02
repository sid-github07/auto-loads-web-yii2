<?php

namespace common\components;

use common\components\ElasticSearch\CarTransporters;
use common\components\ElasticSearch\CarTransportersSuggestions;
use common\components\ElasticSearch\Cities;
use common\components\ElasticSearch\Loads;
use common\components\ElasticSearch\Suggestions;
use Yii;
use yii\elasticsearch\Query;

/**
 * Class ElasticSearch
 *
 * @package common\components
 */
class ElasticSearch
{
    /** @const integer Minimum number of characters that are required to enter in order to start ElasticSearch request */
    const MINIMUM_SEARCH_TEXT_LENGTH = 3;

    /** @const integer Number of milliseconds to wait before starting ElasticSearch request */
    const DEFAULT_DELAY = 250;

    /**
     * Creates new document index
     *
     * @param string $index Document index name
     * @return boolean Whether index was created successfully
     */
    public static function createIndex($index = '')
    {
        if (empty($index)) {
            return false;
        }

        $query = new Query();
        $query->createCommand()->createIndex($index);
        return $query->createCommand()->indexExists($index);
    }

    /**
     * Sets mapping for document type
     *
     * @param string $index Document index name
     * @param string $type Document type name
     * @return boolean|mixed
     */
    public static function setMapping($index = '', $type = '')
    {
        if (empty($index) || empty($type)) {
            return false;
        }

        $query = new Query();
        $mapping = self::getMapping($type);
        $query->createCommand()->setMapping($index, $type, $mapping);
        return $query->createCommand()->getMapping($index, $type);
    }

    /**
     * Returns document type mapping by given document type
     *
     * @param string $type Document type name
     * @return array
     */
    private static function getMapping($type = '')
    {
        switch ($type) {
            case Cities::TYPE:
                return Cities::mapping();
            case Loads::TYPE:
                return Loads::mapping();
            case Suggestions::TYPE:
                return Suggestions::mapping();
            case CarTransporters::TYPE:
                return CarTransporters::mapping();
            case CarTransportersSuggestions::TYPE:
                return CarTransportersSuggestions::mapping();
            default:
                return [];
        }
    }

    /**
     * Filters user loads
     *
     * @param string $phrase City name phrase
     * @param string $token Unique string to identify user load
     * @return array
     */
    public static function filterMyLoads($phrase = '', $token = '')
    {
        $term = Yii::$app->user->isGuest ? ['token' => $token] : ['user_id' => Yii::$app->user->id];
        $loads = Loads::userLoadsSearch($term);
        $ids = Loads::getLoadsCitiesIds($loads);
        $cities = Cities::getUserLoadsCities($phrase, $ids);
        $suggestions = Cities::getLoadCitiesSuggestions($phrase, true);
        $joinCities = array_unique(array_merge($cities, $suggestions), SORT_REGULAR);

        return array_values($joinCities);
    }
}