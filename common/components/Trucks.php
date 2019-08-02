<?php

namespace common\components;

use common\components\ElasticSearch\CarTransporters;
use common\models\CarTransporter;
use common\models\City;
use common\models\Load;
use common\models\LoadCity;
use yii\elasticsearch\Query;

/**
 * Class Trucks
 * @package common\components
 */
class Trucks
{
    private $load;
    private $elasticQueryConditions = [];
    private $trucksList;

    const DEFAULT_RADIUS_KM = 40;
    const MAX_COUNT_OF_TRUCKS = 50;
    const MAX_VALUES_FOR_ELASTIC_CONDITION = 1024;

    /**
     * PotentialHaulier constructor.
     * 1. Get load/unload cities of current LOAD
     * 2. Getting the cities in DEFAULT_RADIUS_KM radius of load/unload-cities (!important DO NOT USE to big radius to avoid time/memory errors)
     * 3. Sort cities by frequency
     * 4. Cut cities according to max_clause_count for ES
     * @param Load $load
     * @throws \Exception
     */
    public function __construct(Load $load)
    {
        $this->load = $load;
        /** @var LoadCity $loadCity */
        foreach ($this->load->loadCities as $loadCity) {
            $city = City::findOne($loadCity->city_id);
            if (!$city instanceof City) {
                throw new \Exception(\Yii::t('alert',
                    'Unable to find city using city_id in MySQL: ' . $loadCity->city_id));
            }
            $cityType = $loadCity->isLoadingCity() ? 'load_location' : 'unload_location';
            // Getting loads in 20km near the city
            $this->elasticQueryConditions[$cityType][] = $this->getElasticQueryConditionForCityArea(
                $city,
                $cityType,
                self::DEFAULT_RADIUS_KM
            );
        }
    }

    /**
     * Creating ES conditions for future query
     * @param City $city
     * @param string $path (load, unload)
     * @param int $radius
     * @return array
     */
    private function getElasticQueryConditionForCityArea(City $city, $path, $radius = self::DEFAULT_RADIUS_KM)
    {
        if ($city->isCountry()) {
            return [
                'nested' => [
                    'path' => $path,
                    'query' => [
                        'bool' => [
                            'must' => [
                                'match' => [
                                    $path . '.country_code' => $city->country_code,
                                ],
                            ],
                        ],
                    ],
                ]
            ];
        } else {
            return [
                'nested' => [
                    'path' => $path,
                    'query' => [
                        'bool' => [
                            'filter' => [
                                'geo_distance' => [
                                    'distance' => sprintf('%dkm', $radius),
                                    $path . '.coordinates' => [
                                        'lat' => $city->latitude,
                                        'lon' => $city->longitude
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }
    }

    /**
     * @return array (structure like suggestion index item)
     */
    public function getTrucks()
    {
        if (is_null($this->trucksList)) {
            $conditions = [
                'bool' => [
                    'must' => [
                        [
                            'bool' => [
                                'should' => [
                                    $this->elasticQueryConditions['load_location']
                                ]
                            ]
                        ],
                        [
                            'bool' => [
                                'should' => [
                                    $this->elasticQueryConditions['unload_location']
                                ]
                            ]
                        ],
                        [
                            'range' => [
                                'date_of_expiry' => [
                                    'gte' => time(),
                                ],
                            ],
                        ],
                        [
                            'range' => [
                                'available_from' => [
                                    'lte' => time(),
                                ],
                            ],
                        ],
                        [
                            'term' => [
                                'visible' => 1
                            ]
                        ],
                        [
                            'term' => [
                                'archived' => 0
                            ]
                        ]
                    ],
                ]
            ];
            if (!is_null(\Yii::$app->getUser()->id)) {
                $conditions['bool']['must'][] = [
                    'bool' => [
                        'must_not' => [
                            [
                                'term' => [
                                    'user_id' => \Yii::$app->getUser()->id
                                ]
                            ]
                        ]
                    ]
                ];
            }
            $trucksList = [];
            foreach ((new Query())->from(CarTransporters::INDEX,
                CarTransporters::TYPE)->query($conditions)->batch() as $batch) {
                $trucksList = array_merge($trucksList, array_column($batch, '_source'));
            }
            // Additional Mysql check for visible/archived
            $visibleAndActiveTrucks = CarTransporter::find()
                ->where(['id' => array_column($trucksList, 'id'), 'visible' => 1, 'archived' => 0])->select('id')->column();
            foreach ($trucksList as $k => $truck) {
                if (!in_array($truck['id'], $visibleAndActiveTrucks)) {
                    unset($trucksList[$k]);
                }
            }
            $this->trucksList = $trucksList;
        }
        return array_slice($this->trucksList, 0, self::MAX_COUNT_OF_TRUCKS);
    }

    /**
     * @return int
     */
    public function getCountOfTrucks()
    {
        return count($this->getTrucks());
    }
}