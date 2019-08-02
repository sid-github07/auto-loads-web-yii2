<?php

namespace common\components\ElasticSearch;

use common\models\City;
use yii\elasticsearch\Query;

/**
 * Class Cities
 *
 * @package common\components\ElasticSearch
 */
class Cities
{
    /** @const string Cities document index */
    const INDEX = 'cities';

    /** @const string Cities document type */
    const TYPE = 'city';

    /** @const integer Maximum number of simple cities */
    const MAX_SIMPLE_CITIES = 4;

    /** @const integer Maximum number of directions */
    const MAX_DIRECTIONS = 3;

    /**
     * Returns cities mapping structure
     *
     * @return array
     */
    public static function mapping()
    {
        return [
            'properties' => [
                'id' => ['type' => 'integer'],
                'name' => ['type' => 'string'],
                'ansi_name' => ['type' => 'string'],
                'alt_name' => ['type' => 'string'],
                'location' => ['type' => 'geo_point'],
                'country_code' => ['type' => 'string'],
                'population' => ['type' => 'integer'],
                'popularity' => ['type' => 'integer'],
                'directions' => [
                    'type' => 'nested',
                    'properties' => [
                        'id' => ['type' => 'integer'],
                        'popularity' => ['type' => 'integer'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Add cities from database to ElasticSearch
     */
    public static function add()
    {
        $query = new Query();
        $cities = City::find()->limit(10000)->offset(0)->all(); // NOTE: increase offset for every limit
        /** @var City $city */
        foreach ($cities as $city) {
            $data = [
                'id' => $city->id,
                'name' => $city->name,
                'ansi_name' => $city->ansi_name,
                'alt_name' => $city->alt_name,
                'location' => [
                    'lat' => $city->latitude,
                    'lon' => $city->longitude,
                ],
                'country_code' => $city->country_code,
                'population' => $city->population,
                'popularity' => 0,
                'directions' => [],
            ];
            $query->createCommand()->insert(self::INDEX, self::TYPE, $data, $city->id);
        }
    }

    /**
     * Adds countries from database to ElasticSearch
     */
    public static function addCountries()
    {
        $query = new Query();
        $countries = City::find()->limit(53)->orderBy(['id' => SORT_DESC])->all();
        /** @var City $country */
        foreach ($countries as $country) {
            $data = [
                'id' => $country->id,
                'name' => $country->name,
                'ansi_name' => $country->ansi_name,
                'alt_name' => $country->alt_name,
                'location' => [
                    'lat' => $country->latitude,
                    'lon' => $country->longitude,
                ],
                'country_code' => $country->country_code,
                'population' => $country->population,
                'popularity' => 0,
                'directions' => [],
            ];
            $query->createCommand()->insert(self::INDEX, self::TYPE, $data, $country->id);
        }
    }

    /**
     * Updates city information
     *
     * @param array $city City information
     */
    private static function update($city = [])
    {
        $query = new Query();
        $query->createCommand()->insert(self::INDEX, self::TYPE, $city['_source'], $city['_id']);
    }

    /**
     * Finds city by given city ID
     *
     * @param null|integer $id City ID
     * @return array|false City information or false if city not found
     */
    public static function findById($id = null)
    {
        $query = new Query();
        $city = $query->from(self::INDEX, self::TYPE)
            ->query([
                'constant_score' => [
                    'filter' => [
                        'term' => [
                            'id' => $id,
                        ],
                    ],
                ],
            ])
            ->one();

        return $city;
    }

    /**
     * Returns simple search cities by given city name phrase
     *
     * @param string $phrase City name phrase
     * @param string $code Country code
     * @return array
     */
    public static function getSimpleSearchCities($phrase = '', $code = '')
    {
        $items = [];
        $cities = self::simpleSearch($phrase, $code);

        foreach ($cities as $city) {
            array_push($items, self::formatItem($city));
        }

        return $items;
    }

    /**
     * Formats city information for one city results item
     *
     * @param array $city
     * @return array
     */
    public static function formatItem($city = [])
    {
        $cityModel = City::findOne($city['_source']['id']);

        return [
            'id' => $city['_source']['id'],
            'name' => (is_null($cityModel) || $cityModel->isCountry() ? \Yii::t('country', $city['_source']['name']) : $city['_source']['name']) . ' (' . $city['_source']['country_code'] . ')',
            'location' => $city['_source']['location'],
            'zoom' => is_null($cityModel) || $cityModel->isCountry() ? 5 : 10,
            'country_code' => $city['_source']['country_code'],
        ];
    }

    /**
     * Searches for cities by given city name phrase
     *
     * @param string $phrase City name phrase
     * @param string $code Country code
     * @return array
     */
    public static function simpleSearch($phrase = '', $code = '')
    {
        $query = new Query();
        $cities = $query->from(self::INDEX, self::TYPE)
            ->query([
                'multi_match' => [
                    'fields' => [
                        'name^10',
                        'ansi_name^8',
                        'alt_name^5',
                    ],
                    'query' => $phrase,
                    'type' => 'phrase_prefix',
                ],
            ])
            ->orderBy([
                'population' => [
                    'order' => 'desc',
                ],
            ])
            ->createCommand()
            ->search();

        return $cities['hits']['hits'];
    }

    /**
     * Searches for popular city by given city name phrase
     *
     * @param string $phrase City name phrase
     * @return array|boolean Popular city information or false if popular city not found
     */
    public static function popularCitySearch($phrase = '')
    {
        $query = new Query();
        $city = $query->from(self::INDEX, self::TYPE)
            ->query([
                'bool' => [
                    'should' => [
                        [
                            'match' => [
                                'ansi_name' => [
                                    'query' => $phrase,
                                    'boost' => 10,
                                ],
                            ],
                        ],
                        [
                            'match' => [
                                'name' => [
                                    'query' => $phrase,
                                    'boost' => 8,
                                ],
                            ],
                        ],
                        [
                            'multi_match' => [
                                'query' => $phrase,
                                'fields' => ['alt_name'],
                                'boost' => 5,
                            ],
                        ],
                    ],
                ],
            ])
            ->orderBy([
                'popularity' => SORT_DESC,
                '_score' => SORT_DESC,
            ])
            ->one();

        return $city;
    }

    /**
     * Updates city popularity
     *
     * @param null|integer $id City ID
     * @param array $directions City directions
     * @return bool Whether city popularity was updated successfully
     */
    public static function updatePopularity($id = null, $directions = [])
    {
        $city = self::findById($id);
        if (!$city) {
            return false;
        }

        self::increasePopularity($city);
        if (!empty($directions)) {
            self::updateDirections($city, $directions);
        }
        self::update($city);

        return true;
    }

    /**
     * Increases popularity for given city
     *
     * @param array $city City information
     */
    private static function increasePopularity(&$city = [])
    {
        $city['_source']['popularity'] += 1;
    }

    /**
     * Updates city directions
     *
     * @param array $city City information
     * @param array $directions City directions
     */
    private static function updateDirections(&$city = [], $directions = [])
    {
        foreach ($directions as $directionCityId) {
            $key = self::getDirectionKey($city, $directionCityId);
            is_null($key) ? self::addDirection($city, $directionCityId) : self::increaseDirectionPopularity($city, $key);
        }
    }

    /**
     * Returns direction key
     *
     * @param array $city City information
     * @param null|integer $id Direction city ID
     * @return integer|null Direction key or null if key not found
     */
    private static function getDirectionKey($city = [], $id = null)
    {
        foreach ($city['_source']['directions'] as $key => $direction) {
            if ($direction['id'] == $id) {
                return $key;
            }
        }
        return null;
    }

    /**
     * Adds new direction to city information
     *
     * @param array $city City information
     * @param null|integer $id Direction city ID
     */
    private static function addDirection(&$city = [], $id = null)
    {
        $city['_source']['directions'][] = [
            'id' => $id,
            'popularity' => 1,
        ];
    }

    /**
     * Increases city direction popularity
     *
     * @param array $city City information
     * @param null|integer $key Direction key
     */
    private static function increaseDirectionPopularity(&$city = [], $key = null)
    {
        $city['_source']['directions'][$key]['popularity'] += 1;
    }

    /**
     * Returns list of cities for loads/unloads suggestions
     *
     * @param string $phrase City name phrase
     * @param boolean $unload Attribute, whether suggestions is for unload
     * @return array
     */
    public static function getLoadCitiesSuggestions($phrase = '', $unload = false)
    {
        $items = [];

        $popularCity = self::popularCitySearch($phrase);
        if (!$popularCity) {
            self::addSimpleCities($phrase, $items);
            return $items;
        }

        $popularCityItem = self::formatItem($popularCity);
        array_push($items, $popularCityItem);

        self::addSimpleCities($phrase, $items, $popularCityItem);

        if ($unload) {
            return $items;
        }

        self::addPopularDirections($items, $popularCity, $popularCityItem);

        return $items;
    }

    /**
     * Adds simple cities to cities items
     *
     * @param string $phrase City name phrase
     * @param array $items Cities items
     * @param array $popularCityItem Popular city item information
     */
    public static function addSimpleCities($phrase = '', &$items = [], $popularCityItem = [])
    {
        $cities = self::simpleSearch($phrase);
        $counter = 0;

        foreach ($cities as $city) {
            if ($counter >= self::MAX_SIMPLE_CITIES) {
                break;
            }

            $item = self::formatItem($city);
            if ($popularCityItem && ($item['id'] == $popularCityItem['id'])) {
                continue;
            }

            array_push($items, $item);
            $counter++;
        }
    }

    /**
     * Adds popular directions to cities items
     *
     * @param array $items Cities items
     * @param array $popularCity Popular city information
     * @param array $popularCityItem Popular city item information
     */
    public static function addPopularDirections(&$items = [], $popularCity = [], $popularCityItem = [])
    {
        $directions = self::getDirections($popularCity);
        $counter = 0;
        foreach ($directions as $direction) {
            if ($counter >= self::MAX_DIRECTIONS) {
                break;
            }

            $city = self::findById($direction['id']);
            if (!$city) {
                continue;
            }

            $item = self::formatItem($city);
            array_push($items, [
                'id' => $popularCityItem['id'] . '-' . $item['id'],
                'name' => $popularCityItem['name'] . ' - ' . $item['name'],
                'popularId' => $popularCityItem['id'],
                'popularName' => $popularCityItem['name'],
                'directionId' => $item['id'],
                'directionName' => $item['name'],
            ]);
            $counter++;
        }
    }

    /**
     * Returns sorted city directions
     *
     * @param array $city City information
     * @return array
     */
    private static function getDirections($city = [])
    {
        usort($city['_source']['directions'], function($a, $b) {
            return $b['popularity'] - $a['popularity'];
        });

        return $city['_source']['directions'];
    }

    /**
     * Formats load/unload cities for adding to ElasticSearch
     *
     * @param array $loadCities Load/unload cities IDs
     * @return array
     */
    public static function formatLoadCities($loadCities = [])
    {
        $cities = [];

        foreach ($loadCities as $loadCityId) {
            $loadCity = self::findById($loadCityId);
            if (!$loadCity) {
                continue;
            }

            array_push($cities, [
                'city_id' => $loadCityId,
                'country_code' => $loadCity['_source']['country_code'],
                'location' => $loadCity['_source']['location'],
            ]);
        }

        return $cities;
    }

    /**
     * Returns city location by given city ID
     *
     * @param null|integer $id City ID
     * @return array
     */
    public static function getLocation($id = null)
    {
        $city = self::findById($id);
        if (!$city) {
            return [
                'lat' => null,
                'lon' => null,
            ];
        }

        return $city['_source']['location'];
    }

    /**
     * Returns user loads cities
     *
     * @param string $phrase City name phrase
     * @param array $ids List of loads IDs
     * @return array
     */
    public static function getUserLoadsCities($phrase = '', $ids = [])
    {
        $cities = self::userLoadsCitiesSearch($phrase, $ids);

        $userCities = [];
        foreach ($cities as $city) {
            array_push($userCities, self::formatItem($city));
        }

        return $userCities;
    }

    /**
     * Searches for user loads cities
     *
     * @param string $phrase City name phrase
     * @param array $ids List of loads IDs
     * @return array
     */
    private static function userLoadsCitiesSearch($phrase = '', $ids = [])
    {
        $query = new Query();
        $cities = $query->from(self::INDEX, self::TYPE)
            ->query([
                'bool' => [
                    'must' => [
                        'terms' => [
                            'id' => $ids,
                        ],
                    ],
                    'should' => [
                        [
                            'match' => [
                                'ansi_name' => [
                                    'query' => $phrase,
                                    'boost' => 10,
                                ],
                            ],
                        ],
                        [
                            'match' => [
                                'name' => [
                                    'query' => $phrase,
                                    'boost' => 8,
                                ],
                            ],
                        ],
                        [
                            'multi_match' => [
                                'query' => $phrase,
                                'fields' => ['alt_name'],
                                'boost' => 5,
                            ],
                        ],
                    ],
                ],
            ])
            ->minScore(1)
            ->orderBy([
                '_score' => SORT_DESC,
            ])
            ->createCommand()
            ->search();

        return $cities['hits']['hits'];
    }

    /**
     * Returns distance between two cities
     *
     * @param integer $firstCityId First city ID
     * @param integer $secondCityId Second city ID
     * @return float
     */
    public static function getDistance($firstCityId = 0, $secondCityId = 0)
    {
        $firstCity = self::findById($firstCityId);
        $secondCity = self::findById($secondCityId);
        $firstCityLocation = $firstCity['_source']['location'];
        $secondCityLocation = $secondCity['_source']['location'];
        $distance = self::calculateDistance($firstCityLocation, $secondCityLocation);

        return $distance;
    }

    /**
     * Calculates distance in kilometers between two cities
     *
     * @see http://www.geodatasource.com/developers/php
     * @param array $location1 First city latitude and longitude coordinates
     * @param array $location2 Second city latitude and longitude coordinates
     * @return float
     */
    private static function calculateDistance($location1, $location2)
    {
        $lat1 = $location1['lat'];
        $lat2 = $location2['lat'];
        $lon1 = $location1['lon'];
        $lon2 = $location2['lon'];
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $distance = $dist * 60 * 1.1515 * 1.609344;

        return $distance;
    }

    /**
     * @param City $city
     * @param int $km
     * @return array
     */
    public static function getCitiesInArea(City $city, $km)
    {
        $condition = [
            'bool' => [
                'filter' => [
                    'geo_distance' => [
                        'distance' => sprintf('%dkm', $km),
                        'location' => ['lat' => $city->latitude, 'lon' => $city->longitude],
                    ],
                ],
            ]
        ];
        if ($city->isCountry()) {
            $condition['bool']['must'] = [
                'match' => [
                    'country_code' => $city->country_code,
                ]
            ];
        }
        $query = (new Query())->from(self::INDEX, self::TYPE)
            ->query($condition);
        $cities = [];
        foreach ($query->batch() as $batch) {
            $cities = array_merge($cities, $batch);
        }
        return $cities;
    }
}
