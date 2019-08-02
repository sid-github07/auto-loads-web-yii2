<?php

namespace common\components\ElasticSearch;

use common\models\Company;
use common\models\Load;
use common\models\LoadCar;
use common\models\LoadCity;
use Yii;
use yii\elasticsearch\Query;

/**
 * Class Suggestions
 *
 * @package common\components\ElasticSearch
 */
class Suggestions
{
    /** @const string Suggestions document index */
    const INDEX = 'suggestions';

    /** @const string Suggestions document type */
    const TYPE = 'suggestion';

    /** @const integer Default popularity value */
    const DEFAULT_POPULARITY = 1;

    /**
     * Returns suggestions mapping structure
     *
     * @return array
     */
    public static function mapping()
    {
        return [
            'properties' => [
                'user_id' => ['type' => 'integer'],
                'search_radius' => ['type' => 'integer'],
                'date' => ['type' => 'integer'],
                'quantity' => ['type' => 'integer'],
                'load' => ['type' => 'integer'],
                'unload' => ['type' => 'integer'],
                'popularity' => ['type' => 'integer'],
                'modification_date' => ['type' => 'integer'],
                'sent_to_user' => ['type' => 'boolean'],
            ],
        ];
    }

    /**
     * Adds search information to ElasticSearch
     *
     * @param array $data
     * @return array
     */
    private static function add($data = [])
    {
        $query = new Query();
        return $query->createCommand()->insert(self::INDEX, self::TYPE, $data);
    }

    /**
     * Updates ElasticSearch search information
     *
     * @param array $data Search information
     * @return array
     */
    private static function update($data = [])
    {
        $query = new Query();
        return $query->createCommand()->insert(self::INDEX, self::TYPE, $data['_source'], $data['_id']);
    }

    /**
     * Searches for current user searches by given load and unload cities IDs
     *
     * @param integer $load Load city ID
     * @param integer $unload Unload city ID
     * @return array
     */
    private static function findByCities($load = 0, $unload = 0)
    {
        $query = new Query();
        $searches = $query->from(self::INDEX, self::TYPE)
            ->query([
                'constant_score' => [
                    'filter' => [
                        'bool' => [
                            'must' => [
                                [
                                    'match' => [
                                        'user_id' => Yii::$app->user->id,
                                    ],
                                ],
                                [
                                    'match' => [
                                        'load' => $load,
                                    ],
                                ],
                                [
                                    'match' => [
                                        'unload' => $unload,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ])
            ->createCommand()
            ->search();

        return $searches['hits']['hits'];
    }

    /**
     * Searches for current user searches by given date range
     *
     * @param integer $startDate Date range start date
     * @param integer $endDate Date range end date
     * @return array
     */
    private static function findByDateRange($startDate = 0, $endDate = 0)
    {
        $query = new Query();
        $searches = $query->from(self::INDEX, self::TYPE)
            ->query([
                'bool' => [
                    'must' => [
                        [
                            'match' => [
                                'user_id' => Yii::$app->user->id,
                            ],
                        ],
                        [
                            'range' => [
                                'modification_date' => [
                                    'gte' => $startDate,
                                    'lte' => $endDate,
                                ],
                            ],
                        ],
                    ],
                ],
            ])
            ->createCommand()
            ->search();

        return $searches['hits']['hits'];
    }

    /**
     * Saves search information data to ElasticSearch
     *
     * @param Load $load Load model
     * @param LoadCar $loadCar Load car model
     * @param LoadCity $loadCity Load city model
     * @return array
     */
    public static function saveSearchInfo(Load $load, LoadCar $loadCar, LoadCity $loadCity)
    {
        $data = self::formSearchInfo($load, $loadCar, $loadCity);
        if (self::isSearchInfoExists($data)) {
            return self::updateSearchInfo($data);
        }

        return self::add($data);
    }

    /**
     * Checks whether search info exists in ElasticSearch
     *
     * @param array $data Current search info data
     * @return boolean
     */
    private static function isSearchInfoExists($data = [])
    {
        $searches = self::findByCities($data['load'], $data['unload']);
        $search = reset($searches);

        return !empty($search);
    }

    /**
     * Updates search information
     *
     * @param array $data Current search info data
     * @return array
     */
    private static function updateSearchInfo($data = [])
    {
        $searches = self::findByCities($data['load'], $data['unload']);
        $search = reset($searches);
        $search['_source'] = self::updateSearchInfoSource($search, $data);
        return self::update($search);
    }

    /**
     * Updates search information source with new data
     *
     * @param array $search Search info in ElasticSearch
     * @param array $data Current search info data
     * @return array
     */
    private static function updateSearchInfoSource($search = [], $data = [])
    {
        $source = $search['_source'];
        $source['search_radius'] = $data['search_radius'];
        $source['date'] = $data['date'];
        $source['quantity'] = $data['quantity'];
        $source['popularity'] += 1;
        $source['modification_date'] = time();
        return $source;
    }

    /**
     * Forms search information data
     *
     * @param Load $load Load model
     * @param LoadCar $loadCar Load car model
     * @param LoadCity $loadCity Load city model
     * @return array
     */
    private static function formSearchInfo(Load $load, LoadCar $loadCar, LoadCity $loadCity)
    {
        return [
            'user_id' => Yii::$app->user->id,
            'search_radius' => $load->searchRadius,
            'date' => empty($load->date) ? 0 : strtotime($load->date),
            'quantity' => $loadCar->quantity,
            'load' => $loadCity->loadCityId,
            'unload' => $loadCity->unloadCityId,
            'popularity' => self::DEFAULT_POPULARITY,
            'modification_date' => time(),
            'sent_to_user' => false,
        ];
    }

    /**
     * Returns current user searches that have been made within 24 hours
     *
     * @return array
     */
    public static function getDaySearches()
    {
        $startDate = self::getDayEarlierDate();
        $endDate = time();
        $searches = self::findByDateRange($startDate, $endDate);

        return $searches;
    }

    /**
     * Returns date that is 24 hours earlier than current time
     *
     * @return false|integer
     */
    private static function getDayEarlierDate()
    {
        return strtotime('-24 hours');
    }

    /**
     * Returns current user previous searches
     *
     * @return array
     */
    public static function getPreviousSearches()
    {
        $startDate = 0;
        $endDate = self::getDayEarlierDate();
        $searches = self::findByDateRange($startDate, $endDate);

        return $searches;
    }

    /**
     * Returns loads IDs by current user sign up city
     *
     * @param Load $load Load model
     * @param LoadCity $loadCity Load city model
     * @param array $loadKeys loads id array
     * @return array
     */
    public static function getUserSignUpCitySearches(Load $load, LoadCity $loadCity, $loadKeys)
    {
        $ids = [];
        /** @var Company $company */
        $company = Company::findUserCompany(Yii::$app->user->id);
        if (is_null($company->city_id)) {
            return $ids;
        }

        if (empty($loadCity->loadCityId) || ($loadCity->loadCityId == $company->city_id)) {
            $ids = Loads::getIdsByLoadCity($company->city_id, $load, $loadCity, $loadKeys);
        }

        foreach ($ids as $key => $id) {
            if (!Load::isLoadVisible(key($id))) {
                unset($ids[$key]);
            }
        }

        return $ids;
    }
    
    /**
     * Returns loads IDs by given user id sign up city
     *
     * @param Load $load Load model
     * @param LoadCity $loadCity Load city model
     * @param integer $user_id given user id
     * @return array
     */
    public static function getUserSignUpCitySusggestions(Load $load, LoadCity $loadCity, $user_id)
    {
        $ids = [];
        /** @var Company $company */
        $company = Company::findUserCompany($user_id);
        if (is_null($company->city_id)) {
            return $ids;
        }

        if (empty($loadCity->loadCityId) || ($loadCity->loadCityId == $company->city_id)) {
            $ids = Loads::getIdsByLoadCity($company->city_id, $load, $loadCity);
        }

        foreach ($ids as $key => $id) {
            if (!Load::isLoadVisible(key($id))) {
                unset($ids[$key]);
            }
        }

        return [
                'direct' => $ids,
                'additional' => [],
                'fullUnload' => [],
            ];;
    }

    /**
     * Finds and returns specific user load suggestions that user does not seen
     *
     * @param integer $userId User ID
     * @return array
     */
    public static function findNotSeenUserSuggestions($userId)
    {
        $query = new Query();
        $search = $query->from(self::INDEX, self::TYPE)
            ->query([
                'bool' => [
                    'must' => [
                        [
                            'match' => [
                                'user_id' => $userId,
                            ],
                        ],
                        [
                            'match' => [
                                'sent_to_user' => false,
                            ],
                        ],
                    ],
                ],
            ])
            ->createCommand()
            ->search();

        $suggestions = [];
        foreach ($search['hits']['hits'] as $result) {
            array_push($suggestions, $result['_source']);
        }

        return $suggestions;
    }

    /**
     * Marks specific user loads suggestions as seen
     *
     * @param integer $userId User ID
     */
    public static function markAsSeen($userId)
    {
        $query = new Query();
        $search = $query->from(self::INDEX, self::TYPE)
            ->query([
                'bool' => [
                    'must' => [
                        [
                            'match' => [
                                'user_id' => $userId,
                            ],
                        ],
                        [
                            'match' => [
                                'sent_to_user' => false,
                            ],
                        ],
                    ],
                ],
            ])
            ->createCommand()
            ->search();

        $suggestions = $search['hits']['hits'];
        foreach ($suggestions as $suggestion) {
            $suggestion['_source']['sent_to_user'] = true;
            self::update($suggestion);
        }

        return;
    }
}