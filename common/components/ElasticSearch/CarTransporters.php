<?php

namespace common\components\ElasticSearch;

use common\models\CarTransporter;
use common\models\CarTransporterCity;
use common\models\City;
use yii\elasticsearch\Query;

/**
 * Class CarTransporters
 *
 * @package common\components\ElasticSearch
 */
class CarTransporters
{
    const INDEX = 'car_transporters';
    const TYPE = 'car_transporter';

    const SEARCH_RESULTS_SIZE = 10000;

    private $radius;
    private $quantity;
    private $availableFrom;
    private $loadLocations;
    private $unloadLocations;

    /**
     * CarTransporters constructor.
     *
     * @param CarTransporter $carTransporter Car transporter model
     * @param CarTransporterCity $carTransporterCity Car transporter city model
     */
    public function __construct(CarTransporter $carTransporter, CarTransporterCity $carTransporterCity)
    {
        $this->setRadius($carTransporter);
        $this->setQuantity($carTransporter);
        $this->setAvailableFrom($carTransporter);
        $this->setLoadLocation($carTransporterCity);
        $this->setUnloadLocation($carTransporterCity);
    }

    /**
     * Sets car transporter search radius
     *
     * @param CarTransporter $carTransporter Car transporter model
     */
    private function setRadius(CarTransporter $carTransporter)
    {
        $this->radius = $carTransporter->radius;
    }

    /**
     * Returns car transporter search radius
     *
     * @return integer
     */
    private function getRadius()
    {
        return $this->radius;
    }

    /**
     * Sets car transporter quantity
     *
     * @param CarTransporter $carTransporter Car transporter model
     */
    private function setQuantity(CarTransporter $carTransporter)
    {
        $this->quantity = $carTransporter->quantity;
    }

    /**
     * Returns car transporter quantity
     *
     * @return null|integer
     */
    private function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Sets date from when car transporter is available
     *
     * @param CarTransporter $carTransporter
     */
    private function setAvailableFrom(CarTransporter $carTransporter)
    {
        $carTransporter->convertAvailableFromDateToTimestamp();
        $this->availableFrom = empty($carTransporter->available_from) ? null : $carTransporter->available_from;
    }

    /**
     * Returns date from when car transporter is available
     *
     * @return null|integer
     */
    private function getAvailableFrom()
    {
        return $this->availableFrom;
    }

    /**
     * Sets car transporter load location
     *
     * @param CarTransporterCity $carTransporterCity Car transporter city model
     */
    private function setLoadLocation(CarTransporterCity $carTransporterCity)
    {
        $this->loadLocations = $carTransporterCity->loadLocation;
    }

    /**
     * Returns car transporter load location
     *
     * @return integer
     */
    private function getLoadLocation()
    {
        return $this->loadLocations;
    }

    /**
     * Sets car transporter unload location
     *
     * @param CarTransporterCity $carTransporterCity Car transporter city model
     */
    private function setUnloadLocation(CarTransporterCity $carTransporterCity)
    {
        $this->unloadLocations = $carTransporterCity->unloadLocation;
    }

    /**
     * Returns car transporter unload location
     *
     * @return integer
     */
    private function getUnloadLocation()
    {
        return $this->unloadLocations;
    }

    /**
     * Returns car transporters mapping structure
     *
     * @return array
     */
    public static function mapping()
    {
        return [
            'properties' => [
                'id' => ['type' => 'integer'],
                'user_id' => ['type' => 'integer'],
                'quantity' => ['type' => 'integer'],
                'available_from' => ['type' => 'integer'],
                'date_of_expiry' => ['type' => 'integer'],
                'visible' => ['type' => 'integer'],
                'archived' => ['type' => 'integer'],
                'load_location' => [
                    'type' => 'nested',
                    'properties' => [
                        'city_id' => ['type' => 'integer'],
                        'country_code' => ['type' => 'string'],
                        'coordinates' => ['type' => 'geo_point'],
                    ],
                ],
                'unload_location' => [
                    'type' => 'nested',
                    'properties' => [
                        'city_id' => ['type' => 'integer'],
                        'country_code' => ['type' => 'string'],
                        'coordinates' => ['type' => 'geo_point'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Adds car transporter information to ElasticSearch
     *
     * @param CarTransporter $carTransporter Car transporter model that information needs to be added to ElasticSearch
     * @param City[] $loadLocations List of user selected car transporter load cities
     * @param City[] $unloadLocations List of user selected car transporter unload cities
     */
    public static function add(CarTransporter $carTransporter, $loadLocations, $unloadLocations)
    {
        $data = [
            'id' => $carTransporter->id,
            'user_id' => $carTransporter->user_id,
            'quantity' => $carTransporter->quantity,
            'available_from' => empty($carTransporter->available_from) ? 0 : $carTransporter->available_from,
            'date_of_expiry' => $carTransporter->date_of_expiry,
            'visible' => $carTransporter->visible,
            'archived' => $carTransporter->archived,
            'load_location' => self::formatLocation($loadLocations),
            'unload_location' => self::formatLocation($unloadLocations),
        ];

        $query = new Query();
        $query->createCommand()->insert(self::INDEX, self::TYPE, $data, $carTransporter->id);
    }

    /**
     * Formats car transporter load or unload locations to add into ElasticSearch
     *
     * @param City[] $cities List of user selected car transporter load or unload cities
     * @return array
     */
    private static function formatLocation($cities)
    {
        $locations = [];
        foreach ($cities as $city) {
            $location = [
                'city_id' => $city->id,
                'country_code' => $city->country_code,
                'coordinates' => [
                    'lat' => $city->latitude,
                    'lon' => $city->longitude,
                ],
            ];
            array_push($locations, $location);
        }
        return $locations;
    }

    /**
     * Searches for car transporters
     *
     * @return array
     */
    public function search()
    {
        $loadLocation = City::findById($this->getLoadLocation());
        $query = new Query();
        $results = $query->from(self::INDEX, self::TYPE)
            ->query([
                'bool' => [
                    'must' => [
                        [
                            'match' => [
                                'visible' => CarTransporter::VISIBLE,
                            ],
                        ],
                        [
                            'match' => [
                                'archived' => CarTransporter::NOT_ARCHIVED,
                            ],
                        ],
                        [
                            'range' => [
                                'date_of_expiry' => [
                                    'gte' => time(),
                                ],
                            ],
                        ],
                        [
                            'bool' => [
                                'should' => [
                                    [
                                        'range' => [
                                            'quantity' => [
                                                'gte' => $this->getQuantity(),
                                            ],
                                        ],
                                    ],
                                    [
                                        'missing' => [
                                            'field' => 'quantity',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'range' => [
                                'available_from' => is_null($this->getAvailableFrom()) ? ['gte' => 0] : ['lte' => $this->getAvailableFrom()],
                            ],
                        ],
                        $this->getLocationStructure(CarTransporterCity::TYPE_LOAD),
                        $this->getLocationStructure(CarTransporterCity::TYPE_UNLOAD),
                    ],
                ],
            ])
            ->orderBy([
                '_geo_distance' => [
                    'nested_path' => 'load_location',
                    'load_location.coordinates' => [
                        'lon' => $loadLocation->longitude,
                        'lat' => $loadLocation->latitude,
                    ],
                    'order' => 'asc',
                    'unit' => 'km',
                    'distance_type' => 'plane',
                ],
            ])
            ->createCommand()
            ->search(['size' => self::SEARCH_RESULTS_SIZE]);
        return $results['hits']['hits'];
    }

    /**
     * Returns load/unload (depending on type) location search structure
     *
     * @param integer $type Car transporter city type
     * @return array
     */
    private function getLocationStructure($type)
    {
        $locationId = $type == CarTransporterCity::TYPE_LOAD ? $this->getLoadLocation() : $this->getUnloadLocation();
        $city = City::findById($locationId);
        if ($city->isCountry()) {
            return $this->getCountryStructure($type, $city->country_code);
        }

        return $this->getCityStructure($type, $city);
    }

    /**
     * Returns load/unload (depending on type) location search structure when location is country
     *
     * @param integer $type Car transporter city type
     * @param string $countryCode Car transporter load/unload location country code
     * @return array
     */
    private function getCountryStructure($type, $countryCode)
    {
        $path = ($type == CarTransporterCity::TYPE_LOAD ? '' : 'un') . 'load_location';

        return [
            'nested' => [
                'path' => $path,
                'query' => [
                    'bool' => [
                        'must' => [
                            'match' => [
                                $path . '.country_code' => $countryCode,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Returns load/unload (depending on type) location search structure when location is city
     *
     * @param integer $type Car transporter city type
     * @param City $city City model
     * @return array
     */
    private function getCityStructure($type, City $city)
    {
        $path = ($type == CarTransporterCity::TYPE_LOAD ? '' : 'un') . 'load_location';
        $country = City::findCountry($city->country_code);

        return [
            'nested' => [
                'path' => $path,
                'query' => [
                    'bool' => [
                        'should' => [
                            [
                                'match' => [
                                    $path . '.city_id' => $country->id,
                                ],
                            ],
                            [
                                'bool' => [
                                    'must' => [
                                        'match_all' => [],
                                    ],
                                    'filter' => [
                                        'geo_distance' => [
                                            'distance' => $this->getRadius() . 'km',
                                            'distance_type' => 'plane',
                                            $path . '.coordinates' => [
                                                'lat' => $city->latitude,
                                                'lon' => $city->longitude,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Finds car transporter by given car transporter ID
     *
     * @param null|integer $id Car transporter ID
     * @return array|boolean Car transporter or false if not found
     */
    public static function findOne($id)
    {
        $query = new Query();
        $carTransporter = $query->from(self::INDEX, self::TYPE)
            ->query([
                'constant_score' => [
                    'filter' => [
                        'term' => [
                            'id' => $id,
                        ],
                    ],
                ],
            ])->one();

        return $carTransporter;
    }

    /**
     * Updates specific car transporter entry in ElasticSearch
     *
     * @param array $carTransporter Car transporter data that need to be updated
     * @return integer
     */
    public static function update($carTransporter)
    {
        $query = new Query();
        $response = $query->createCommand()
                          ->insert(self::INDEX, self::TYPE, $carTransporter['_source'], $carTransporter['_id']);
        return $response['_shards']['successful'];
    }

    /**
     * Updates specific car transporter available from date
     *
     * @param integer $id Specific car transporter ID
     * @param null|integer $availableFromDate New car transporter available from date value
     * @return boolean|integer Whether car transporter available from date was updated successfully
     */
    public static function updateAvailableFromDate($id, $availableFromDate)
    {
        $carTransporter = self::findOne($id);
        if (!$carTransporter) {
            return false;
        }

        $carTransporter['_source']['available_from'] = $availableFromDate ? $availableFromDate : 0;
        return self::update($carTransporter);
    }

    /**
     * Updates specific car transporter quantity
     *
     * @param integer $id Specific car transporter ID
     * @param null|integer $quantity New car transporter quantity value
     * @return boolean|integer Whether car transporter quantity was updated successfully
     */
    public static function updateQuantity($id, $quantity)
    {
        $carTransporter = self::findOne($id);
        if (!$carTransporter) {
            return false;
        }

        $carTransporter['_source']['quantity'] = $quantity;
        return self::update($carTransporter);
    }

    /**
     * Updates specific car transporter visibility
     *
     * @param integer $id Specific car transporter ID
     * @param null|integer $visibility New car transporter visibility value
     * @return boolean|integer Whether car transporter quantity was updated successfully
     */
    public static function updateVisibility($id, $visibility)
    {
        $carTransporter = self::findOne($id);
        if (!$carTransporter) {
            return false;
        }

        $carTransporter['_source']['visible'] = $visibility;
        return self::update($carTransporter);
    }

    /**
     * Updates specific car transporter date of expiry
     *
     * @param integer $id Specific car transporter ID
     * @param null|integer $dateOfExpiry New car transporter date of expiry value
     * @return boolean|integer Whether car transporter date of expiry was updated successfully
     */
    public static function updateDateOfExpiry($id, $dateOfExpiry)
    {
        $carTransporter = self::findOne($id);
        if (!$carTransporter) {
            return false;
        }

        $carTransporter['_source']['date_of_expiry'] = $dateOfExpiry;
        return self::update($carTransporter);
    }

    /**
     * Archives specific car transporter
     *
     * @param integer $id Specific car transporter ID
     * @return boolean|integer Whether car transporter was archived successfully
     */
    public static function archive($id)
    {
        $carTransporter = self::findOne($id);
        if (!$carTransporter) {
            return false;
        }

        $carTransporter['_source']['visible'] = CarTransporter::INVISIBLE;
        $carTransporter['_source']['archived'] = CarTransporter::ARCHIVED;
        return self::update($carTransporter);
    }
    
    /**
     * Deactivates all active ElasticSearch loads
     */
    public static function deactivateAllActivated()
    {
        $dbCarTransporters = CarTransporter::findAll([
            'visible' => CarTransporter::ACTIVATED,
        ]);

        $ids = [];
        foreach ($dbCarTransporters as $load) {
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
        $carTransporters = self::findOne($ids);
        if (!$carTransporters) {
            return false;
        }

        foreach($carTransporters as $carTransporter) {
            $carTransporter['_source']['active'] = CarTransporter::INACTIVE;
            self::update($carTransporter);
        }
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
        $model = $query->from(self::INDEX, self::TYPE)
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

        return $model;
    }

    /**
     * Removes specific transporter
     *
     * @param null|integer $id Load ID that needs to be removed
     * @return boolean Whether load was removed successfully
     */
    public static function removeByAdmin($id)
    {
        $model = self::findById($id);
        if (!$model) {
            return false;
        }

        $model['_source']['visible'] = CarTransporter::NOT_ACTIVATED;
        self::update($model);

        return true;
    }
}