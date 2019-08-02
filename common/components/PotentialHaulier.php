<?php

namespace common\components;

use common\components\ElasticSearch\Loads;
use common\models\City;
use common\models\Load;
use common\models\LoadCity;
use common\models\LoadPreview;
use common\models\User;
use yii\elasticsearch\Query;
use common\models\Company;

/**
 * Class PotentialHaulier
 * @package common\components
 */
class PotentialHaulier
{
    private $load;
    private $elasticQueryConditions = [];
    private $similarLoads;
    private $potentialHaulierList;

    const DEFAULT_RADIUS_KM = 40;
    const MAX_COUNT_OF_POTENTIAL_HAULIERS = 20;
    const MIN_COUNT_OF_VIEWS = 5;

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
        $this->log(str_pad('', 30, '='));
        $this->log(sprintf("Searching potential hauliers for load: [%d]...", $load->id));
        $this->load = $load;
        /** @var LoadCity $loadCity */
        foreach ($this->load->loadCities as $loadCity) {
            $city = City::findOne($loadCity->city_id);
            if (!$city instanceof City) {
                throw new \Exception(\Yii::t('alert',
                    'Unable to find city using city_id in MySQL: ' . $loadCity->city_id));
            }
            $cityType = $loadCity->isLoadingCity() ? 'load' : 'unload';
            $this->log(
                sprintf("New city for the load found: %s [%d] (city type: %s)",
                    $city->name,
                    $loadCity->city_id,
                    $cityType)
            );
            // Getting loads in X-km near the city
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
            $this->log("Making query...");
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
                        ]
                    ],
                ]
            ];
            $similarLoads = [];
            foreach ((new Query())->from(Loads::INDEX, Loads::TYPE)->query($conditions)->batch() as $batch) {
                $similarLoads = array_merge($similarLoads, array_column($batch, '_id'));
            }
            $uniqueLoads = array_unique($similarLoads);
            $this->log(sprintf('Found %d similar loads', count($uniqueLoads)));
            $this->similarLoads = $uniqueLoads;
        }
        return $this->similarLoads;
    }

    /**
     * Get list of potential hauliers (list of user_id)
     * @return array
     */
    public function getPotentialHauliersByHistoryOfSearch()
    {
        if (is_null($this->potentialHaulierList)) {
            $similarLoads = $this->getSimilarLoads();
            if (empty($similarLoads)) {
                $this->log('No similar loads -> no potentials...Ending...');
                return [];
            }
            $query = LoadPreview::find()
                ->select(['user_id', 'counter' => 'COUNT(*)'])
                ->where(['load_id' => $this->getSimilarLoads()])
                ->groupBy('user_id')
                ->orderBy('counter DESC')
                ->having(sprintf('counter >= %d', self::MIN_COUNT_OF_VIEWS))
                ->limit(self::MAX_COUNT_OF_POTENTIAL_HAULIERS);
            if (!is_null($this->load->user_id)) {
                $query->andWhere(['!=', 'user_id', $this->load->user_id]);
            }
            $this->potentialHaulierList = $query->asArray()->all();
            $this->log(sprintf('Found %d potentials', count($this->potentialHaulierList)));
        }
        return $this->potentialHaulierList;
    }

    /**
     * Returns array of potential hauliers previews sorted by preview
     * Each element of an array include a subarray [created_at (Y-m-d H:i or -----), user_id, ip (if not exist then -----), company, last_seen]
     * @return array
     */
    public function getPotentialHauliersPreviews()
    {
        $userIdAndCounter = array_column($this->getPotentialHauliersByHistoryOfSearch(), 'counter', 'user_id');
        $previews = LoadPreview::find()->select(['created_at', 'user_id', 'ip'])->where([
            'load_id' => $this->load->id,
            'user_id' => array_keys($userIdAndCounter)
        ])->asArray()->indexBy('user_id')->all();
        // Sorting previews
        $sortedPreviews = [];
        foreach ($userIdAndCounter as $user_id => $counter) {
            if (!isset($previews[$user_id])) {
                $sortedPreviews[] = ['user_id' => $user_id];
            } else {
                $sortedPreviews[] = $previews[$user_id];
            }
        }
        foreach ($sortedPreviews as &$preview) {
            $userModel = User::findOne($preview['user_id']);
            try {
                $preview['company'] = Company::getCompany($preview['user_id'])->getTitleByType() . ' | ' . $userModel->getNameAndSurname();
            } catch (\Exception $e) {
                $preview['company'] = 'Not found exception';
            }
            $preview['last_seen'] = $userModel->convertLastLoginToString();
            $preview['similar_views'] = $userIdAndCounter[$userModel->id];
            foreach (['created_at', 'ip'] as $k) {
                if (!isset($preview[$k])) {
                    $preview[$k] = str_pad('', 5, '-');
                } elseif ($k === 'created_at') {
                    $preview[$k] = date('Y-m-d H:i', $preview[$k]);
                }
            }
        }
        return array_values($sortedPreviews);
    }

    /**
     * @return int
     */
    public function getCountOfPotential()
    {
        return count($this->getPotentialHauliersByHistoryOfSearch());
    }

    /**
     * @param string $message
     */
    private function log($message)
    {
        \Yii::info($message, 'potential-hauliers');
    }
}