<?php

namespace common\components\ElasticSearch;

use common\models\City;
use common\models\Load;
use common\models\LoadCar;
use common\models\LoadCity;
use yii\elasticsearch\Query;

/**
 * Class Loads
 *
 * @package common\components\ElasticSearch
 */
class Loads
{
    /** @const string Loads document index */
    const INDEX = 'loads';

    /** @const string Loads document type */
    const TYPE = 'load';
    
    const LIMIT = 1000;

    /**
     * Returns loads mapping structure
     *
     * @return array
     */
    public static function mapping()
    {
        return [
            'properties' => [
                'id' => ['type' => 'integer'],
                'user_id' => ['type' => 'integer'],
                'type' => ['type' => 'integer'],
                'date' => ['type' => 'integer'],
                'status' => ['type' => 'integer'],
                'active' => ['type' => 'integer'],
                'token' => ['type' => 'string'],
                'quantity' => ['type' => 'integer'],
                'load' => [
                    'type' => 'nested',
                    'properties' => [
                        'city_id' => ['type' => 'integer'],
                        'country_code' => ['type' => 'string'],
                        'location' => ['type' => 'geo_point'],
                    ],
                ],
                'unload' => [
                    'type' => 'nested',
                    'properties' => [
                        'city_id' => ['type' => 'integer'],
                        'country_code' => ['type' => 'string'],
                        'location' => ['type' => 'geo_point'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Adds load data to ElasticSearch
     *
     * @param array $data Load data
     */
    private static function add($data = [])
    {
        $query = new Query();
        $query->createCommand()->insert(self::INDEX, self::TYPE, $data, $data['id']);
    }

    /**
     * Updates ElasticSearch load
     *
     * @param array $data Load data
     */
    private static function update($data = [])
    {
        $query = new Query();
        $query->createCommand()->insert(self::INDEX, self::TYPE, $data['_source'], $data['_id']);
    }

    /**
     * Finds load by given load ID
     *
     * @param null|integer $id Load ID
     * @return array|boolean Load information or false if load not found
     */
    private static function findById($id = null)
    {
        $query = new Query();
        $load = $query->from(self::INDEX, self::TYPE)
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

        return $load;
    }

    /**
     * Finds loads by given loads IDs
     *
     * @param array $ids List of loads IDs
     * @return array
     */
    private static function findByIds($ids = [])
    {
        $query = new Query();
        $load = $query->from(self::INDEX, self::TYPE)
            ->query([
                'constant_score' => [
                    'filter' => [
                        'terms' => [
                            'id' => $ids,
                        ],
                    ],
                ],
            ])
            ->createCommand()
            ->search();

        return $load['hits']['hits'];
    }

    /**
     * Searches for loads by given load city ID
     *
     * @todo refactor
     * @param integer $cityId Load city ID
     * @param Load $load Load model
     * @param LoadCity $loadCity Load city model
     * @param array $loadKeys loads id array
     * @return array
     */
    private static function findByLoadCity($cityId = 0, Load $load, LoadCity $loadCity, $loadKeys)
    {
        $queryStructure = [
            'bool' => [
                'must' => [
                    [
                        'terms' => [
                            'id' => $loadKeys,
                        ],
                    ],
                    [
                        'match' => [
                            'status' => Load::ACTIVE,
                        ],
                    ],
                    [
                        'match' => [
                            'active' => Load::ACTIVATED,
                        ],
                    ],
                    [
                        'nested' => [
                            'path' => 'load',
                            'query' => [
                                'constant_score' => [
                                    'filter' => [
                                        'term' => [
                                            'load.city_id' => $cityId,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        if (!empty($loadCity->unloadCityId) && $loadCity->validate(['unloadCityId'])) {
            array_push($queryStructure['bool']['must'], [
                'nested' => [
                    'path' => 'unload',
                    'query' => [
                        'constant_score' => [
                            'filter' => [
                                'term' => [
                                    'unload.city_id' => $loadCity->unloadCityId,
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
        }

        if (is_null($load->date)) {
            $queryStructure['bool']['should'] = [
                [
                    'range' => [
                        'date' => [
                            'gte' => time(),
                        ],
                    ],
                ],
                [
                    'match' => [
                        'date' => 0,
                    ],
                ],
            ];
        } else {
            $date = strtotime($load->date);
            if ($date && $load->filterDate) {
                array_push($queryStructure['bool']['must'], [
                    'match' => [
                        'date' => $date,
                    ],
                ]);
            } else {
                $queryStructure['bool']['should'] = [
                    [
                        'range' => [
                            'date' => [
                                'gte' => time(),
                            ],
                        ],
                    ],
                    [
                        'match' => [
                            'date' => 0,
                        ],
                    ],
                ];
            }
        }

        $query = new Query();
        $loads = $query->from(self::INDEX, self::TYPE)
            ->query($queryStructure)
            ->createCommand()
            ->search(['size' => 20]);

        return $loads['hits']['hits'];
    }

    /**
     * Adds new load to ElasticSearch
     *
     * @param Load $load Load model
     * @param integer $quantity Load cars quantity
     * @param array $loadCities List of load cities
     * @param array $unloadCities List of unload cities
     */
    public static function addLoad(Load $load, $quantity = 0, $loadCities = [], $unloadCities = [])
    {
        $data = [
            'id' => $load->id,
            'user_id' => $load->user_id,
            'type' => $load->type,
            'date' => $load->date,
            'status' => $load->status,
            'active' => $load->active,
            'token' => $load->token,
            'quantity' => $quantity >= Load::TYPE_LIMIT ? 0 : $quantity,
            'load' => Cities::formatLoadCities($loadCities),
            'unload' => Cities::formatLoadCities($unloadCities),
        ];
        self::add($data);
    }

    /**
     * Updates load date
     *
     * @param null|integer $id Load ID
     * @param integer|false $date Load date in timestamp or false if date cannot be converted to timestamp
     * @return boolean Whether load date was updated successfully
     */
    public static function updateDate($id = null, $date = 0)
    {
        $load = self::findById($id);
        if (!$load) {
            return false;
        }

        $load['_source']['date'] = $date ? $date : 0;
        self::update($load);

        return true;
    }

    /**
     * Updates load quantity
     *
     * @param null|integer $id Load ID
     * @param integer $quantity Load cars quantity
     * @return boolean Whether load quantity was updated successfully
     */
    public static function updateQuantity($id = null, $quantity = 0)
    {
        $load = self::findById($id);
        if (!$load) {
            return false;
        }

        $load['_source']['quantity'] = $quantity;
        self::update($load);

        return true;
    }

    /**
     * Updates load activity
     *
     * @param null|integer $id Load ID
     * @param null|integer $activity Load activity
     * @param null|string $token Unique string to identify load
     * @param null|integer $userId User ID
     * @return bool Whether load activity was updated successfully
     */
    public static function updateActivity($id = null, $activity = null, $token = null, $userId = null)
    {
        $load = self::findById($id);
        if (!$load) {
            return false;
        }

        if (!self::isOwner($load, $token, $userId)) {
            return false;
        }

        $load['_source']['active'] = $activity;
        self::update($load);

        return true;
    }

    /**
     * Updates load status
     *
     * @param null|integer $id Load ID
     * @param null|integer $status Load status
     * @param null|string $token Unique string to identify load
     * @param null|integer $userId User ID
     * @return boolean Whether load status was updated successfully
     */
    public static function updateStatus($id = null, $status = null, $token = null, $userId = null)
    {
        $load = self::findById($id);
        if (!$load) {
            return false;
        }

        if (!self::isOwner($load, $token, $userId)) {
            return false;
        }

        $load['_source']['status'] = $status;
        self::update($load);

        return true;
    }

    /**
     * Checks whether user is load owner
     *
     * @param array $load Load information
     * @param null|string $token Unique string to identify load
     * @param null $userId User ID
     * @return boolean
     */
    private static function isOwner($load = [], $token = null, $userId = null)
    {
        if (is_null($token) && is_null($userId)) {
            return false;
        }

        if (is_null($token)) {
            return $load['_source']['user_id'] == $userId;
        }

        if (is_null($userId)) {
            return $load['_source']['token'] == $token;
        }

        return false;
    }

    /**
     * Returns direct loads IDs
     *
     * @param Load $load Load model
     * @param LoadCar $loadCar Load car model
     * @param LoadCity $loadCity Load city model
     * @param array $loadKeys loads id array
     * @return array
     */
    public static function getDirectLoads(Load $load, LoadCar $loadCar, LoadCity $loadCity, $loadKeys)
    {
        $loadStructure = self::getLocationSearchStructure($load, $loadCity, 'load');
        $unloadStructure = self::getLocationSearchStructure($load, $loadCity, 'unload');
        $loadLocation = Cities::getLocation($loadCity->loadCityId);
        $directLoads = self::directSearch($load, $loadCar, $loadStructure, $unloadStructure, $loadLocation, $loadKeys);
        $ids = self::getDirectLoadsIds($directLoads);
        $idsWithSearchRequest = self::getDirectLoadSearchRequest($ids, $loadCity->loadCityId, $loadCity->unloadCityId, $loadCar['quantity']);

        return $idsWithSearchRequest;
    }

    /**
     * Searches for direct transportation loads
     *
     * @param Load $load Load model
     * @param LoadCar $loadCar Load car model
     * @param array $loadStructure Load location search structure
     * @param array $unloadStructure Unload location search structure
     * @param array $loadLocation Load city location
     * @param array $loadKeys loads id array
     * @return array
     */
    private static function directSearch(Load $load, LoadCar $loadCar, $loadStructure, $unloadStructure, $loadLocation, $loadKeys)
    {
        $query = new Query();
        $loads = $query->from(self::INDEX, self::TYPE)
            ->query([
                'bool' => [
                    'must' => [
                        [
                            'terms' => [
                                'id' => $loadKeys,
                            ],
                        ],
                        [
                            'match' => [
                                'active' => Load::ACTIVATED,
                            ],
                        ],
                        [
                            'match' => [
                                'status' => Load::ACTIVE,
                            ],
                        ],
                        self::getQuantitySearchStructure($loadCar),
                        self::getDateSearchStructure($load),
                        $loadStructure,
                        $unloadStructure,
                    ],
                ],
            ])
            ->orderBy([
                '_geo_distance' => [
                    'nested_path' => 'load',
                    'load.location' => $loadLocation,
                    'order' => 'asc',
                    'unit' => 'km',
                    /*
                     * Can be one of these:
                     * sloppy_arc (default),
                     * arc (slightly more precise but significantly slower)
                     * plane (faster, but inaccurate on long distances)
                     */
                    'distance_type' => 'plane',
                ],
            ])
            ->createCommand()
            ->search(['size' => self::LIMIT]);

        return $loads['hits']['hits'];
    }

    /**
     * Returns nested block structure for load/unload search
     *
     * @param Load $load Load model
     * @param string $type Load or unload
     * @param array $location Load/unload city location
     * @return array
     */
    private static function getNestedSearchStructure(Load $load, $type = '', $location = [])
    {
        return [
            'nested' => [
                'path' => $type,
                'query' => [
                    'bool' => [
                        'must' => [
                            'match_all' => [],
                        ],
                        'filter' => [
                            'geo_distance' => [
                                'distance' => $load->searchRadius . 'km',
                                $type . '.location' => $location,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
    
    /**
     * Sets load type based on searchable quantity
     * 
     * @param LoadCar $loadCar Load car model
     * @return array
     */
    private static function getQuantitySearchStructure(LoadCar $loadCar) 
    {
        if ($loadCar->quantity >= Load::TYPE_LIMIT) {
            return [
                'match' => [
                    'type' => Load::TYPE_FULL,
                ],
            ];
        }
        return [
            'range' => [
                 'quantity' => [
                     'lte' => $loadCar->quantity,  
                 ]
            ],
            [
                'match' => [
                    'type' => Load::TYPE_PARTIAL,
                ],
            ]
        ];
    }

    /**
     * Returns date search structure
     *
     * @param Load $load Load model
     * @return array
     */
    private static function getDateSearchStructure(Load $load)
    {
        $date = empty($load->date) ? 0 : strtotime($load->date);

        if ($date && $load->filterDate) {
            return [
                'match' => [
                    'date' => $date,
                ],
            ];
        }

        return [
            'bool' => [
                'should' => [
                ],
            ],
        ];
    }

    /**
     * Returns list of direct loads IDs
     *
     * @param array $directLoads List of direct loads
     * @return array
     */
    private static function getDirectLoadsIds($directLoads = [])
    {
        $loads = [];
        foreach ($directLoads as $directLoad) {
            $id = $directLoad['_source']['id'];
            if (Load::isLoadVisible($id) && !in_array($id, $loads)) {
                array_push($loads, $id);
            }
        }
        
        return $loads;
    }
    
    /**
     * Returns list of direct loads IDs with search request
     * 
     * @param array $directLoadsIds List of direct loads
     * @param string $loadCityRequest
     * @param string $unloadCityRequest
     * @param string $requestedLoadQuantity
     * @return array
     */
    private static function getDirectLoadSearchRequest ($directLoadsIds = [], $loadCityRequest, $unloadCityRequest, $requestedLoadQuantity)
    {
        $loadsWithSearch = [];
        foreach ($directLoadsIds as $id => $directLoadId) {
            $id = self::getDirectLoadsSearchRequestStructure($directLoadId, $loadCityRequest, $unloadCityRequest, $requestedLoadQuantity);
            if (!in_array($id, $loadsWithSearch)) {
                array_push($loadsWithSearch, $id);
            }
        }
        
        return $loadsWithSearch;
    }
    
    /**
     * Creates structure for direct loads with search request
     * 
     * @param integer $id
     * @param string $loadCityRequest
     * @param string $unloadCityRequest
     * @param string $requestLoadQuantity
     * @return array
     */
    private static function getDirectLoadsSearchRequestStructure ($id, $loadCityRequest, $unloadCityRequest, $requestLoadQuantity)
    {
        return $idWithSearchRequest = [
            $id => [
                'loadCityRequest' => $loadCityRequest,
                'unloadCityRequest' => $unloadCityRequest,
                'loadRequestQuantity' => $requestLoadQuantity
            ]
        ];
    }
    
    /**
     * Creates structure for direct loads in search results page
     * 
     * @return array
     */
    public static function getDirectLoadsSearchResultsStructure()
    {
        return $directLoad['id'] = [];
    }

    /**
     * Returns additional loads groups
     *
     * @param Load $load Load model
     * @param LoadCar $loadCar Load car model
     * @param LoadCity $loadCity Load city model
     * @param array $loadKeys loads id array
     * @return array
     */
    public static function getAdditionalLoads(Load $load, LoadCar $loadCar, LoadCity $loadCity, $loadKeys)
    {
        $additionalLoads = self::getAdditionalLoadsInfo($load, $loadCar, $loadCity, $loadKeys);
        if ($loadCar->quantity < Load::TYPE_LIMIT) {
            $groupedAdditionalLoads = self::groupAdditionalLoads($loadCar, $additionalLoads, $loadCar->quantity, false);
            $idsWithSearchRequest = self::getAdditionalLoadsSearchRequest($groupedAdditionalLoads, $loadCity->loadCityId, $loadCity->unloadCityId, $loadCar['quantity']);

            return $idsWithSearchRequest;
        }
        $groupedAdditionalLoads = self::groupAdditionalLoads($loadCar, $additionalLoads, Load::TYPE_LIMIT, true);
        return self::getAdditionalLoadsSearchRequest($groupedAdditionalLoads, $loadCity->loadCityId, $loadCity->unloadCityId, $loadCar['quantity']);
    }

    /**
     * Returns list of additional loads information
     *
     * @param Load $load Load model
     * @param LoadCar $loadCar Load car model
     * @param LoadCity $loadCity Load city model
     * @param array $loadKeys loads id array
     * @return array
     */
    private static function getAdditionalLoadsInfo(Load $load, LoadCar $loadCar, LoadCity $loadCity, $loadKeys)
    {
        $loadStructure = self::getLocationSearchStructure($load, $loadCity, 'load');
        $unloadStructure = self::getLocationSearchStructure($load, $loadCity, 'unload');
        $additionalLoads = self::additionalSearch($load, $loadCar, $loadStructure, $unloadStructure, $loadKeys);

        // Remove invisible loads
        foreach ($additionalLoads as $key => $additionalLoad) {
            if (!Load::isLoadVisible($additionalLoad['_source']['id'])) {
                unset($additionalLoads[$key]);
            }
        }

        return $additionalLoads;
    }
    
    /**
     * Returns list of additional loads with search request
     * 
     * @param array $additionalLoads
     * @param string $requestedLoadCity
     * @param string $requestedUnloadCity
     * @param string $requestedQuantity
     * @return array
     */
    private static function getAdditionalLoadsSearchRequest($additionalLoads, $requestedLoadCity, $requestedUnloadCity, $requestedQuantity)
    {
        $searchRequest = [
            'loadCityRequest' => $requestedLoadCity,
            'unloadCityRequest' => $requestedUnloadCity,
            'loadRequestQuantity' => $requestedQuantity
        ];
        $searchRequestedLoads = [];
        foreach ($additionalLoads as $additionalLoad) {
            if (!empty($additionalLoad)) {
                $additionalLoad['searchRequest'] = $searchRequest;
                array_push($searchRequestedLoads, $additionalLoad);
            }
        }

        return $searchRequestedLoads;
    }

    /**
     * Searches for additional loads
     *
     * @param Load $load Load model
     * @param LoadCar $loadCar Load car model
     * @param array $loadStructure Load location search structure
     * @param array $unloadStructure Unload location search structure
     * @param array $loadKeys loads id array
     * @return array
     */
    private static function additionalSearch(Load $load, LoadCar $loadCar, $loadStructure, $unloadStructure, $loadKeys)
    {
        $query = new Query();
        $loads = $query->from(self::INDEX, self::TYPE)
            ->query([
                'bool' => [
                    'must' => [
                        [
                            'terms' => [
                                'id' => $loadKeys,
                            ],
                        ],
                        [
                            'match' => [
                                'active' => Load::ACTIVATED,
                            ],
                        ],
                        [
                            'match' => [
                                'status' => Load::ACTIVE,
                            ],
                        ],
                        [
                            'match' => [
                                'type' => Load::TYPE_PARTIAL,
                            ],
                        ],
                        [
                            'range' => [
                                'quantity' => [
                                    'lte' => $loadCar->quantity,
                                ],
                            ],
                        ],
                        self::getDateSearchStructure($load),
                        $loadStructure,
                        $unloadStructure,
                    ],
                ],
            ])
            ->createCommand()
            ->search();

        return $loads['hits']['hits'];
    }

    /**
     * Groups additional loads
     *
     * Each group consists of different combination of additional loads IDs
     *
     * @param LoadCar $loadCar Load car model
     * @param array $additionalLoads List of additional loads information
     * @param integer $limit Number of empty spaces in transporter
     * @param boolean $empty Attribute, whether transporter is completely empty
     * @return array
     */
    private static function groupAdditionalLoads(LoadCar $loadCar, $additionalLoads = [], $limit = 0, $empty = false)
    {
        if (self::countTotalQuantity($additionalLoads) < $limit) {
            return []; // Transporter cannot fill all empty spaces
        }

        $loads = [];
        $combinations = self::getCombinations($additionalLoads, $empty);
        foreach ($combinations as $combination) {
            $quantity = self::countCombinationQuantity($combination);
            if (self::isTransporterFilledCompletely($loadCar, $quantity, $empty)) {
                if (self::validateAdditionalDate($combination)) {
                    $additionalLoadsCombination = [
                        $combination,
                        'searchRequest' => []
                    ];
                    $loads = $additionalLoadsCombination;
                }
            }
        }

        return $loads;
    }

    /**
     * Counts total number of load cars
     *
     * @param array $loads List of loads
     * @return integer
     */
    private static function countTotalQuantity($loads = [])
    {
        $quantity = 0;
        foreach ($loads as $load) {
            $quantity += $load['_source']['quantity'];
        }

        return $quantity;
    }

    /**
     * Returns unique additional loads IDs combinations from given list of additional loads information
     *
     * @param array $additionalLoads List of additional loads information
     * @param boolean $empty Attribute, whether transporter is completely empty
     * @return array
     */
    private static function getCombinations($additionalLoads = [], $empty = false)
    {
        $combinations = [];
        $ids = self::getIds($additionalLoads);
        for ($index = 0; $index < count($ids); $index++) {
            self::combinations($ids, $index+1, $combinations);
        }

        $uniqueCombinations = self::removeDuplicates($combinations, 2, ($empty ? 2 : 3));
        return $uniqueCombinations;
    }

    /**
     * Returns list of loads IDs from given list of loads information
     *
     * @param array $loads List of loads
     * @return array
     */
    private static function getIds($loads = [])
    {
        $ids = [];
        foreach ($loads as $load) {
            array_push($ids, $load['_source']['id']);
        }

        return $ids;
    }
    
    /**
     * Returns list of loads IDs with load and unload city from given list of loads information
     *
     * @param array $loads List of loads
     * @return array
     */
    private static function getIdsWithCities($loads = [])
    {
        $ids = [];
        foreach ($loads as $load) {
            array_push($ids, self::getSignUpCitySuggestionsStructure($load['_source']));
        }
        return $ids;
    }
    
    /**
     * Formats structure for sign up city suggestions
     * 
     * @param array $source
     * @return array
     */
    private static function getSignUpCitySuggestionsStructure($source = [])
    {
        return [
            $source['id'] => [
                'load' => $source['load'],
                'unload' => $source['unload'],
            ],
        ];
    }
    
    /**
     * Formats structure for direct load suggestions
     * 
     * @param integer $id
     * @param array $loadCities
     * @return array
     */
    public static function getDirectLoadsSuggestionsStructure($id, $loadCities = [])
    {
        $structure = [
            $id => $loadCities,
        ];
        return $structure;
    }

    /**
     * Combines all possible unique array values combinations
     *
     * @param array $array Target array
     * @param integer $level Array level in multidimensional array
     * @param array $combinations Result of array combination
     * @param array $current Current array (because recursion is being used)
     */
    private static function combinations($array = [], $level = 1, &$combinations = [], $current = [])
    {
        $count = count($array);
        for ($index = 0; $index < $count; $index++) {
            $newArray = array_merge($current, [$array[$index]]);
            if ($level == 1) {
                sort($newArray);
                if (!in_array($newArray, $combinations)) {
                    $combinations[] = $newArray;
                }
            } else {
                if ($level <= 3) {
                    self::combinations($array, $level - 1, $combinations, $newArray);
                } else {
                    break;
                }
            }
        }
    }

    /**
     * Removes duplicates from additional loads combinations
     *
     * @param array $combinations List of all possible additional loads combinations
     * @param integer $min Minimum number of loads that combination may contain
     * @param integer $max Maximum number of loads that combination may contain
     * @return array
     */
    private static function removeDuplicates($combinations = [], $min = 2, $max = 3)
    {
        $unique = [];
        foreach ($combinations as $combination) {
            $count = count($combination);
            if ((!self::hasDuplicates($combination)) && ($count >= $min) && ($count <= $max)) {
                array_push($unique, $combination);
            }
        }

        return $unique;
    }

    /**
     * Checks whether array has duplicates
     *
     * @param array $array Target array
     * @return boolean
     */
    private static function hasDuplicates($array = [])
    {
        $duplicates = [];
        foreach ($array as $value) {
            if (array_key_exists($value, $duplicates)) {
                return true;
            }
            $duplicates[$value] = true;
        }

        return false;
    }

    /**
     * Counts total number of cars for additional load combination
     *
     * @param array $combination List of load IDs
     * @return integer
     */
    private static function countCombinationQuantity($combination = [])
    {
        $count = 0;
        foreach ($combination as $id) {
            $load = self::findById($id);
            if (!$load) {
                continue;
            }
            $quantity = $load['_source']['quantity'];
            $count += empty($quantity) ? Load::TYPE_LIMIT : $quantity;
        }

        return $count;
    }

    /**
     * Checks whether transporter will be filled completely with given cars quantity
     *
     * @param LoadCar $loadCar Load car model
     * @param integer $quantity Number of cars for transporter
     * @param boolean $empty Attribute, whether transporter is completely empty
     * @return boolean
     */
    private static function isTransporterFilledCompletely(LoadCar $loadCar, $quantity = 0, $empty = false)
    {
        if ($empty) {
            return ($quantity >= Load::TYPE_LIMIT) && ($quantity <= LoadCar::QUANTITY_MAX_VALUE);
        }

        return $quantity == $loadCar->quantity;
    }

    /**
     * Validates additional loads combinations date
     *
     * @todo refactor
     * @param array $ids List of additional loads combinations
     * @return boolean
     */
    private static function validateAdditionalDate($ids = [])
    {
        $loads = self::findByIds($ids);
        $firstDate = null;
        $secondDate = null;
        $thirdDate = null;
        foreach ($loads as $load) {
            $date = $load['_source']['date'];
            if (is_null($firstDate)) {
                $firstDate = $date;
                continue;
            }

            if (is_null($secondDate)) {
                $secondDate = $date;
                if (count($loads) > 2) {
                    continue;
                } else {
                    return (($firstDate == $secondDate) || ($firstDate == 0) || ($secondDate == 0));
                }
            }

            $thirdDate = $date;
            return (
                (($firstDate == $secondDate) && ($secondDate == $thirdDate)) ||
                (($firstDate == $secondDate) && ($thirdDate == 0)) ||
                (($firstDate == $thirdDate) && ($secondDate == 0)) ||
                (($firstDate != 0) && ($secondDate == 0) && $thirdDate == 0) ||
                (($firstDate == 0) && ($secondDate == $thirdDate)) ||
                (($firstDate == 0) && ($secondDate != 0) && ($thirdDate == 0)) ||
                (($firstDate == 0) && ($secondDate == 0) && ($thirdDate != 0))
            );
        }

        return false;
    }

    /**
     * Returns full unload loads
     *
     * @param Load $load Load model
     * @param LoadCar $loadCar Load car model
     * @param LoadCity $loadCity Load city model
     * @param array $loadKeys loads id array
     * @return array
     */
    public static function getFullUnloadLoads(Load $load, LoadCar $loadCar, LoadCity $loadCity, $loadKeys)
    {
        $loads = self::prepareFullUnloadLoads($load, $loadCar, $loadCity, [], $loadKeys);
        $unloads = self::prepareFullUnloadUnloads($load, $loadCar, $loadCity, $loads, [], $loadKeys);
        $fullUnloadLoads = self::combineFullUnloadLoads($load->searchRadius, $loads, $unloads);
        $idsWithSearchRequest = self::getFullUnloadRequestedLoads($fullUnloadLoads, $loadCity->loadCityId, $loadCity->unloadCityId, $loadCar['quantity']);

        return $idsWithSearchRequest;
    }

    /**
     * Returns a list of full unload loads with search request
     * 
     * @param array $fullUnloadLoads
     * @param string $requestedLoadCity
     * @param string $requestedUnloadCity
     * @param string $requestedQuantity
     * @return array
     */
    private static function getFullUnloadRequestedLoads($fullUnloadLoads, $requestedLoadCity, $requestedUnloadCity, $requestedQuantity)
    {
        $searchRequest = [
            'loadCityRequest' => $requestedLoadCity,
            'unloadCityRequest' => $requestedUnloadCity,
            'loadRequestQuantity' => $requestedQuantity
        ];
        $searchRequestedLoads = [];
        
        foreach ($fullUnloadLoads as $fullUnloadLoad) {
            $fullUnloadLoad['searchRequest'] = $searchRequest;
            array_push($searchRequestedLoads, $fullUnloadLoad);
        }
        
        return $searchRequestedLoads;
    }
    
    /**
     * Prepares full unload loads for combination
     *
     * @param Load $load Load model
     * @param LoadCar $loadCar Load car model
     * @param LoadCity $loadCity Load city model
     * @param array $loads Full unload loads container
     * @param array $loadKeys loads id array
     * @return array
     */
    private static function prepareFullUnloadLoads(Load $load, LoadCar $loadCar, LoadCity $loadCity, $loads = [], $loadKeys)
    {
        $loadStructure = self::getLocationSearchStructure($load, $loadCity, 'load');
        list($gte, $lte, $exact) = self::getFullUnloadQuantityRange($loadCar->quantity);
        $searchResults = self::fullUnloadLoadsSearch($load, $gte, $lte, $exact, $loadStructure, $loadKeys);

        foreach ($searchResults as $fullUnloadLoad) {
            $source = $fullUnloadLoad['_source'];
            $id = $source['id'];
            if (Load::isLoadVisible($id) && !array_key_exists($id, $loads)) {
                $loads[$id] = $source;
            }
        }

        return $loads;
    }

    /**
     * Returns full unload loads search quantity range
     *
     * @param integer $quantity Full unload loads quantity
     * @return array
     */
    private static function getFullUnloadQuantityRange($quantity)
    {
        $gte = ($quantity == 1 ? 1 : ($quantity >= 2 && $quantity <= 7 ? $quantity - 1 : ($quantity >= 8 ? 7 : 0)));
        $lte = ($quantity == 1 ? 1 : ($quantity >= 2 && $quantity <= 7 ? $quantity : ($quantity >= 8 ? 7 : 0)));
        $exact = ($quantity == 1 ? 1 : ($quantity >= 2 && $quantity <= 7 ? $quantity : ($quantity >= 8 ? 0 : 0)));

        return [$gte, $lte, $exact];
    }

    /**
     * Searches for full unload loads
     *
     * @param Load $load Load model
     * @param integer $gte Quantity value must be greater or equal to this value
     * @param integer $lte Quantity value must be less or equal to this value
     * @param integer $exact Quantity value must be exact equal to this value
     * @param array $loadStructure Load location search structure
     * @param array $loadKeys loads id array
     * @return array
     */
    private static function fullUnloadLoadsSearch(Load $load, $gte, $lte, $exact, $loadStructure, $loadKeys)
    {
        $query = new Query();
        $loads = $query->from(self::INDEX, self::TYPE)
            ->query([
                'bool' => [
                    'must' => [
                        [
                            'terms' => [
                                'id' => $loadKeys,
                            ],
                        ],
                        [
                            'match' => [
                                'active' => Load::ACTIVATED,
                            ],
                        ],
                        [
                            'match' => [
                                'status' => Load::ACTIVE,
                            ],
                        ],
                        [
                            'bool' => [
                                'should' => [
                                    [
                                        'range' => [
                                            'quantity' => [
                                                'gte' => $gte,
                                                'lte' => $lte,
                                            ],
                                        ],
                                    ],
                                    [
                                        'match' => [
                                            'quantity' => $exact,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        self::getDateSearchStructure($load),
                        $loadStructure,
                    ],
                ],
            ])
            ->createCommand()
            ->search();

        return $loads['hits']['hits'];
    }

    /**
     * Prepares full unload unloads for combination
     *
     * @param Load $load Load model
     * @param LoadCar $loadCar Load car model
     * @param LoadCity $loadCity Load city model
     * @param array $fullUnloadLoads Full unload loads container
     * @param array $unloads Full unload unloads container
     * @param array $loadKeys loads id array
     * @return array
     */
    private static function prepareFullUnloadUnloads(Load $load, LoadCar $loadCar, LoadCity $loadCity, $fullUnloadLoads = [], $unloads = [], $loadKeys)
    {
        $unloadStructure = self::getLocationSearchStructure($load, $loadCity, 'unload');
        foreach ($fullUnloadLoads as $loadId => $fullUnloadLoad) {
            $unloadCity = current($fullUnloadLoad['unload']);
            $loadLocation = $unloadCity['location'];
            list($gte, $lte, $exact) = self::getFullUnloadQuantityRange($loadCar->quantity);
            $searchResults = self::fullUnloadUnloadsSearch($load, $gte, $lte, $exact, $loadLocation, $unloadStructure, $loadKeys);
            foreach ($searchResults as $fullUnloadUnload) {
                $source = $fullUnloadUnload['_source'];
                $id = $source['id'];
                if (Load::isLoadVisible($id) && !array_key_exists($id, $unloads)) {
                    $unloads[$id] = $source;
                }
            }
        }

        return $unloads;
    }

    /**
     * Searches for full unload unloads
     *
     * @param Load $load Load model
     * @param integer $gte Quantity value must be greater or equal to this value
     * @param integer $lte Quantity value must be less or equal to this value
     * @param integer $exact Quantity value must be exact equal to this value
     * @param array $loadLocation Load location coordinates
     * @param array $unloadStructure Unload location search structure
     * @param array $loadKeys loads id array
     * @return array
     */
    private static function fullUnloadUnloadsSearch(Load $load, $gte, $lte, $exact, $loadLocation, $unloadStructure, $loadKeys)
    {
        $query = new Query();
        $unloads = $query->from(self::INDEX, self::TYPE)
            ->query([
                'bool' => [
                    'must' => [
                        [
                            'terms' => [
                                'id' => $loadKeys,
                            ],
                        ],
                        [
                            'match' => [
                                'active' => Load::ACTIVATED,
                            ],
                        ],
                        [
                            'match' => [
                                'status' => Load::ACTIVE,
                            ],
                        ],
                        [
                            'bool' => [
                                'should' => [
                                    [
                                        'range' => [
                                            'quantity' => [
                                                'gte' => $gte,
                                                'lte' => $lte,
                                            ],
                                        ],
                                    ],
                                    [
                                        'match' => [
                                            'quantity' => $exact,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        self::getDateSearchStructure($load),
                        self::getNestedSearchStructure($load, 'load', $loadLocation),
                        $unloadStructure,
                    ],
                ],
            ])
            ->createCommand()
            ->search();

        return $unloads['hits']['hits'];
    }

    /**
     * Combines full unload loads
     *
     * @param integer $searchRadius Load search radius
     * @param array $loads Full unload loads
     * @param array $unloads Full unload unloads
     * @param array $fullUnloadLoads Final full unload loads container
     * @return array
     */
    private static function combineFullUnloadLoads($searchRadius, $loads, $unloads, $fullUnloadLoads = [])
    {
        foreach ($loads as $loadId => $load) {
            foreach ($unloads as $unloadId => $unload) {
                foreach ($load['unload'] as $unloadCity) {
                    foreach ($unload['load'] as $loadCity) {
                        if ($unloadCity['city_id'] == $loadCity['city_id'] ||
                            Cities::getDistance($loadCity['city_id'], $unloadCity['city_id']) <= $searchRadius) {
                            if (($load['date']) == 0 || ($unload['date']) == 0 || ($load['date'] <= $unload['date'])) {
                                $fullUnloadLoad = [
                                    $loadId,
                                    $unloadId,
                                    'searchRequest' => []
                                ];
                                array_push($fullUnloadLoads, $fullUnloadLoad);
                            }
                        }
                    }
                }
            }
        }

        return $fullUnloadLoads;
    }

    /**
     * Returns given loads cities IDs
     *
     * @param array $loads List of loads
     * @return array
     */
    public static function getLoadsCitiesIds($loads = [])
    {
        $ids = [];
        foreach ($loads as $load) {
            foreach ($load['_source']['load'] as $item) {
                if (!in_array($item['city_id'], $ids)) {
                    array_push($ids, $item['city_id']);
                }
            }

            foreach ($load['_source']['unload'] as $item) {
                if (!in_array($item['city_id'], $ids)) {
                    array_push($ids, $item['city_id']);
                }
            }
        }

        return $ids;
    }

    /**
     * Searches for user loads
     *
     * @param array $term Attribute, to identify user. Could be user ID or token
     * @return array
     */
    public static function userLoadsSearch($term = [])
    {
        $query = new Query();
        $loads = $query->from(self::INDEX, self::TYPE)
            ->query([
                'constant_score' => [
                    'filter' => [
                        'term' => $term,
                    ],
                ],
            ])
            ->createCommand()
            ->search();

        return $loads['hits']['hits'];
    }

    /**
     * Returns loads IDs by given load city ID
     *
     * @param integer $cityId Load city ID
     * @param Load $load Load model
     * @param LoadCity $loadCity Load city model
     * @param array $loadKeys loads id array
     * @return array
     */
    public static function getIdsByLoadCity($cityId = 0, Load $load, LoadCity $loadCity, $loadKeys)
    {
        $loads = self::findByLoadCity($cityId, $load, $loadCity, $loadKeys);
        $ids = self::getIdsWithCities($loads);

        return $ids;
    }

    /**
     * Returns round trip loads
     *
     * @param integer $cityId Current round trip city ID
     * @param integer $distance Radius around the current round trip city in kilometers
     * @return array
     */
    public static function getRoundTripLoads($cityId = 0, $distance = 0)
    {
        $location = Cities::getLocation($cityId);
        $loads = self::roundTripSearch($distance, $location);

        return $loads;
    }

    /**
     * Searches for round trip
     *
     * @param integer $distance Radius around the current round trip city in kilometers
     * @param array $location City latitude and longitude coordinates
     * @return array
     */
    private static function roundTripSearch($distance = 0, $location = [])
    {
        $query = new Query();
        $loads = $query->from(self::INDEX, self::TYPE)
            ->query([
                'bool' => [
                    'must' => [
                        [
                            'match' => [
                                'active' => Load::ACTIVATED,
                            ],
                        ],
                        [
                            'match' => [
                                'status' => Load::ACTIVE,
                            ],
                        ],
                        [
                            'nested' => [
                                'path' => 'load',
                                'query' => [
                                    'bool' => [
                                        'must' => [
                                            'match_all' => [],
                                        ],
                                        'filter' => [
                                            'geo_distance' => [
                                                'distance' => $distance . 'km',
                                                'load.location' => $location,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ])
            ->createCommand()
            ->search();

        return $loads['hits']['hits'];
    }

    /**
     * Separates loads to partial loads and full loads
     *
     * @param array $loads List of loads
     * @return array
     */
    public static function separateLoadsByType($loads = [])
    {
        $partialLoads = [];
        $fullLoads = [];

        foreach ($loads as $load) {
            $source = $load['_source'];
            if (self::isLoadTypeFull($source)) {
                $fullLoads[$source['id']] = $source;
            } else {
                $partialLoads[$source['id']] = $source;
            }
        }

        return [$partialLoads, $fullLoads];
    }

    /**
     * Checks whether load type is full load
     *
     * @param array $source Load source
     * @return boolean
     */
    private static function isLoadTypeFull($source = [])
    {
        return $source['type'] == Load::TYPE_FULL;
    }
    
    /**
     * Deactivates all active ElasticSearch loads
     */
    public static function deactivateAllActivated()
    {
        $dbLoads = Load::findAll([
            'status' => Load::ACTIVE,
            'active' => Load::ACTIVATED,
        ]);

        $ids = [];
        foreach ($dbLoads as $load) {
            $ids[] = $load->id;
        }
        
        self::deactivateByIds($ids);
    }
    
    /**
     * Set activity to inactive of loads having ids
     * 
     * @param array $ids id array
     */
    public static function deactivateByIds($ids = [])
    {
        $loads = self::findByIds($ids);
        if (!$loads) {
            return false;
        }

        foreach($loads as $load) {
            $load['_source']['active'] = Load::INACTIVE;
            self::update($load);
        }
    }

    /**
     * Adds country code to ElasticSearch load and unload cities
     */
    public static function addCountryCode()
    {
        $query = new Query();
        $loads = $query->from(self::INDEX, self::TYPE)
            ->query(['match_all' => []])
            ->all();

        foreach ($loads as $load) {
            $source = $load['_source'];
            if (self::hasCountryCode($source)) {
                continue;
            }

            self::removeById($source['id']);

            $data = [
                'id' => $source['id'],
                'user_id' => $source['user_id'],
                'type' => $source['type'],
                'date' => $source['date'],
                'status' => $source['status'],
                'active' => $source['active'],
                'token' => $source['token'],
                'quantity' => $source['quantity'],
                'load' => self::formatLoadCities($source['load']),
                'unload' => self::formatLoadCities($source['unload']),
            ];
            self::add($data);
        }
    }

    /**
     * Checks whether load has country code
     *
     * @param array $source Information about the load
     * @return boolean
     */
    private static function hasCountryCode($source)
    {
        return isset($source['load'][0]['country_code']);
    }

    /**
     * Removes specific ElasticSearch load
     *
     * @param integer $id Load ID in ElasticSearch
     */
    private static function removeById($id)
    {
        $options = [
            'constant_score' => [
                'filter' => [
                    'term' => [
                        'id' => $id,
                    ],
                ],
            ],
        ];

        $query = new Query();
        $query->from(self::INDEX, self::TYPE)->delete(null, $options);
    }

    /**
     * Adds country code to load or unload city
     *
     * @param array $cities Load or unload cities
     * @return array
     */
    private static function formatLoadCities($cities)
    {
        $loadCities = [];
        foreach ($cities as $city) {
            $thisCity = Cities::findById($city['city_id']);
            array_push($loadCities, [
                'city_id' => $city['city_id'],
                'country_code' => $thisCity['_source']['country_code'],
                'location' => $city['location'],
            ]);
        }

        return $loadCities;
    }

    /**
     * Returns locations structure for search when searching by city or by country
     *
     * @param Load $load Load model
     * @param LoadCity $loadCity Load city model
     * @param string $type Load or unload
     * @return array
     */
    private static function getLocationSearchStructure(Load $load, LoadCity $loadCity, $type)
    {
        $cityId = ($type === 'load') ? $loadCity->loadCityId : $loadCity->unloadCityId;
        $city = City::findById($cityId);
        if ($city->isCountry()) {
            return self::getCountryCodeSearchStructure($type, $city->country_code);
        }

        $location = Cities::getLocation($cityId);
        return self::getNestedSearchStructure($load, $type, $location);
    }

    /**
     * Returns search structure when searching by country
     *
     * @param string $type Load or unload
     * @param string $code Searchable country code
     * @return array
     */
    private static function getCountryCodeSearchStructure($type, $code)
    {
        return [
            'nested' => [
                'path' => $type,
                'query' => [
                    'bool' => [
                        'must' => [
                            'match' => [
                                $type . '.country_code' => $code,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Removes specific load
     *
     * @param null|integer $id Load ID that needs to be removed
     * @return boolean Whether load was removed successfully
     */
    public static function removeByAdmin($id)
    {
        $load = self::findById($id);
        if (!$load) {
            return false;
        }

        $load['_source']['status'] = Load::INACTIVE;
        $load['_source']['active'] = Load::NOT_ACTIVATED;
        self::update($load);

        return true;
    }
}
