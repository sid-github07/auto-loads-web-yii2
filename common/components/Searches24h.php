<?php

namespace common\components;

use common\components\ElasticSearch\Loads;
use common\models\City;
use common\models\Load;
use common\models\LoadCity;
use common\models\LoadPreview;
use yii\elasticsearch\Query;

/**
 * Class Searches24h
 * @package common\components
 */
class Searches24h
{
    private $load;
    private $elasticQueryConditions = [];
    private $similarLoads;
    private $searches24hList;

    const DEFAULT_RADIUS_KM = 40;
    const MAX_COUNT_OF_USERS = 50;
    const MAX_VALUES_FOR_ELASTIC_CONDITION = 1024;

    /**
     * Searches24h constructor.
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
            $cityType = $loadCity->isLoadingCity() ? 'load' : 'unload';
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
                                    $path . '.location' => [
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
     * Send query to ES, getting similar loads with load/unload cities in 20km radius of the original provided data
     * @return array
     */
    private function getSimilarLoads()
    {
        if (is_null($this->similarLoads)) {
            $conditions = [
                'bool' => [
                    'must' => [
                        [
                            'bool' => [
                                'should' => [
                                    $this->elasticQueryConditions['load']
                                ]
                            ]
                        ],
                        [
                            'bool' => [
                                'should' => [
                                    $this->elasticQueryConditions['unload']
                                ]
                            ]
                        ],
                    ],
                ]
            ];
            if (!is_null(\Yii::$app->user->id)) {
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
            $similarLoads = [];
            foreach ((new Query())->from(Loads::INDEX, Loads::TYPE)->query($conditions)->batch() as $batch) {
                foreach (array_column($batch, '_source', '_id') as $_id => $_source) {
                    $similarLoads[$_id] = $_source;
                }
            }
            $this->similarLoads = $similarLoads;
        }
        return $this->similarLoads;
    }

    /**
     * @return array (structure like suggestion index item)
     */
    public function getSearchesLast24h()
    {
        if (is_null($this->searches24hList)) {
            $similarLoads = $this->getSimilarLoads();
            if (empty($similarLoads)) {
                return [];
            }
            $query = LoadPreview::find()
                ->where(['load_id' => array_keys($this->getSimilarLoads())])
                ->andWhere(['>=', 'updated_at', time() - 24 * 60 * 60])
//                ->groupBy('user_id')
                ->limit(self::MAX_COUNT_OF_USERS);
            if (!is_null($this->load->user_id)) {
                $query->andWhere(['!=', 'user_id', $this->load->user_id]);
            }
            $result = $query->asArray()->all();
            $searches24hList = [];
            foreach ($result as $data) {
                $searches24hList[] = [
                    'user_id' => $data['user_id'],
                    'updated_at' => $data['updated_at'],
                    'source' => $this->getSimilarLoads()[$data['load_id']]
                ];
            }
            $this->searches24hList = $searches24hList;
        }
        return $this->searches24hList;
    }

    /**
     * @return int
     */
    public function getCountOfSearchesLast24h()
    {
        return count($this->getSearchesLast24h());
    }
}