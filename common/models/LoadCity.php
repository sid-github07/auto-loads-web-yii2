<?php

namespace common\models;

use common\components\ElasticSearch\Cities;
use kartik\icons\Icon;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%load_city}}".
 *
 * @property integer $id
 * @property integer $load_id
 * @property integer $city_id
 * @property string $load_postal_code
 * @property string $unload_postal_code
 * @property integer $type
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property City $city
 * @property Load $load
 */
class LoadCity extends ActiveRecord
{
    /** @const integer Unloading city */
    const UNLOADING = 0;

    /** @const integer Loading city */
    const LOADING = 1;

    /** @const integer max lenght for load and unload city postal code */
    const POSTAL_CODE_MAX_LENGTH = 8;

    /** @const string Model scenario when user announces load and must select load and unload cities */
    const SCENARIO_ANNOUNCE_CLIENT = 'announce-client';

    /** @const string Model scenario when selected load and unload cities must be saved to database */
    const SCENARIO_ANNOUNCE_SERVER = 'announce-server';

    /** @const string Model scenario when client searches loads */
    const SCENARIO_SEARCH_CLIENT = 'search-client';

    /** @const string Model scenario when client filters loads suggestions */
    const SCENARIO_CLIENT_FILTERS_LOADS_SUGGESTIONS = 'client-filters-loads-suggestions';

    /** @const string Model scenario when client searches for round trips */
    const SCENARIO_CLIENT_SEARCHES_ROUND_TRIPS = 'client-searches-round-trips';

    /** @const string Model scenario when administrator filters loads */
    const SCENARIO_ADMIN_FILTERS_LOADS = 'admin-filters-loads';

    /** @const string Model scenario when system migrates load cities data from one database to another */
    const SCENARIO_SYSTEM_MIGRATES_LOAD_CITIES_DATA = 'system-migrates-load-cities-data';

    const SCENARIO_SYSTEM_MIGRATES_LOAD_CITY = 'system-migrates-load-city';

    /** @const string Model scenario when user filters its own loads */
    const SCENARIO_USER_FILTERS_MY_LOADS = 'user-filters-my-loads';

    /** @var array|integer List of selected load cities */
    public $loadCityId;

    /** @var array|integer List of selected unload cities */
    public $unloadCityId;

    /** @var string My loads filter attribute */
    public $myLoadsFilter;

    /** @var string Load country code */
    public $loadCountry;

    /** @var string Unload country code */
    public $unloadCountry;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%load_city}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_ANNOUNCE_CLIENT => [
                'loadCityId',
                'unloadCityId',
                'load_postal_code',
                'unload_postal_code',
            ],
            self::SCENARIO_ANNOUNCE_SERVER => [
                'load_id',
                'city_id',
                'loadPostalCode',
                'unloadPostalCode',
                'type',
                'created_at',
                'updated_at'
            ],
            self::SCENARIO_SEARCH_CLIENT => [
                'loadCityId',
                'unloadCityId',
            ],
            self::SCENARIO_CLIENT_FILTERS_LOADS_SUGGESTIONS => [
                'loadCityId',
                'unloadCityId',
            ],
            self::SCENARIO_CLIENT_SEARCHES_ROUND_TRIPS => [
                'city_id',
            ],
            self::SCENARIO_ADMIN_FILTERS_LOADS => [
                'loadCityId',
                'unloadCityId',
                'loadCountry',
                'unloadCountry',
            ],
            self::SCENARIO_SYSTEM_MIGRATES_LOAD_CITIES_DATA => [
                'id',
                'load_id',
                'city_id',
                'type',
                'created_at',
                'updated_at',
            ],
            self::SCENARIO_USER_FILTERS_MY_LOADS => [
                'myLoadsFilter',
            ],
            self::SCENARIO_SYSTEM_MIGRATES_LOAD_CITY => [
                'id',
                'load_id',
                'city_id',
                'type',
                'created_at',
                'updated_at',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Load ID
            ['load_id', 'required', 'message' => Yii::t('app', 'LOAD_CITY_LOAD_ID_IS_REQUIRED')],
            ['load_id', 'integer', 'message' => Yii::t('app', 'LOAD_CITY_LOAD_ID_IS_NOT_INTEGER')],
            [
                'load_id',
                'exist',
                'targetClass' => Load::className(),
                'targetAttribute' => ['load_id' => 'id'],
                'message' => Yii::t('app', 'LOAD_CITY_LOAD_ID_IS_NOT_EXIST')
            ],

            // City ID
            ['city_id', 'required', 'message' => Yii::t('app', 'LOAD_CITY_CITY_ID_IS_REQUIRED')],
            ['city_id', 'integer', 'message' => Yii::t('app', 'LOAD_CITY_CITY_ID_IS_NOT_INTEGER')],
            [
                'city_id',
                'exist',
                'targetClass' => City::className(),
                'targetAttribute' => ['city_id' => 'id'],
                'message' => Yii::t('app', 'LOAD_CITY_CITY_ID_IS_NOT_EXIST')
            ],

            // Load Postal Code
            [
                'load_postal_code',
                'string',
                'max' => self::POSTAL_CODE_MAX_LENGTH,
                'tooLong' => Yii::t('app', 'POSTAL_CODE_IS_TOO_LONG', [
                    'length' => self::POSTAL_CODE_MAX_LENGTH,
                ]),
                'message' => Yii::t('app', 'LOAD_TOKEN_IS_NOT_STRING')
            ],

            // Unload Postal Code
            [
                'unload_postal_code',
                'string',
                'max' => self::POSTAL_CODE_MAX_LENGTH,
                'tooLong' => Yii::t('app', 'POSTAL_CODE_IS_TOO_LONG', [
                    'length' => self::POSTAL_CODE_MAX_LENGTH,
                ]),
                'message' => Yii::t('app', 'POSTAL_CODE_IS_NOT_STRING')
            ],


            // Type
            ['type', 'required', 'message' => Yii::t('app', 'LOAD_CITY_TYPE_IS_REQUIRED')],
            ['type', 'integer', 'message' => Yii::t('app', 'LOAD_CITY_TYPE_IS_NOT_INTEGER')],
            [
                'type',
                'in',
                'range' => [self::UNLOADING, self::LOADING],
                'message' => Yii::t('app', 'LOAD_CITY_TYPE_IS_NOT_IN_RANGE')
            ],

            // Created at
            ['created_at', 'integer', 'message' => Yii::t('app', 'LOAD_CITY_CREATED_AT_IS_NOT_INTEGER')],

            // Updated at
            ['updated_at', 'integer', 'message' => Yii::t('app', 'LOAD_CITY_UPDATED_AT_IS_NOT_INTEGER')],

            // Load city ID
            [
                'loadCityId',
                'string',
                'message' => Yii::t('app', 'LOAD_CITY_LOAD_CITY_ID_IS_NOT_STRING'),
                'on' => self::SCENARIO_ADMIN_FILTERS_LOADS
            ],
            [
                'loadCityId',
                'required',
                'message' => Yii::t('app', 'LOAD_CITY_LOAD_CITY_ID_IS_REQUIRED'),
                'except' => [
                    self::SCENARIO_CLIENT_FILTERS_LOADS_SUGGESTIONS,
                    self::SCENARIO_ADMIN_FILTERS_LOADS,
                ]
            ],
            [
                'loadCityId',
                'each',
                'rule' => [
                    'exist',
                    'targetClass' => City::className(),
                    'targetAttribute' => 'id',
                    'message' => Yii::t('app', 'LOAD_CITY_LOAD_CITY_ID_IS_NOT_EXIST'),
                ],
                'except' => [
                    self::SCENARIO_SEARCH_CLIENT,
                    self::SCENARIO_CLIENT_FILTERS_LOADS_SUGGESTIONS,
                    self::SCENARIO_ADMIN_FILTERS_LOADS,
                ]
            ],
            [
                'loadCityId',
                'exist',
                'targetClass' => City::className(),
                'targetAttribute' => 'id',
                'message' => Yii::t('app', 'LOAD_CITY_LOAD_CITY_ID_IS_NOT_EXIST'),
                'on' => [
                    self::SCENARIO_SEARCH_CLIENT,
                    self::SCENARIO_CLIENT_FILTERS_LOADS_SUGGESTIONS,
                    self::SCENARIO_ADMIN_FILTERS_LOADS,
                ]
            ],

            // Unload city ID
            [
                'unloadCityId',
                'string',
                'message' => Yii::t('app', 'LOAD_CITY_UNLOAD_CITY_ID_IS_NOT_STRING'),
                'on' => self::SCENARIO_ADMIN_FILTERS_LOADS
            ],
            [
                'unloadCityId',
                'required',
                'message' => Yii::t('app', 'LOAD_CITY_UNLOAD_CITY_ID_IS_REQUIRED'),
                'except' => [
                    self::SCENARIO_CLIENT_FILTERS_LOADS_SUGGESTIONS,
                    self::SCENARIO_ADMIN_FILTERS_LOADS,
                ]
            ],
            [
                'unloadCityId',
                'each',
                'rule' => [
                    'exist',
                    'targetClass' => City::className(),
                    'targetAttribute' => 'id',
                    'message' => Yii::t('app', 'LOAD_CITY_UNLOAD_CITY_ID_IS_NOT_EXIST'),
                ],
                'except' => [
                    self::SCENARIO_SEARCH_CLIENT,
                    self::SCENARIO_CLIENT_FILTERS_LOADS_SUGGESTIONS,
                    self::SCENARIO_ADMIN_FILTERS_LOADS,
                ]
            ],
            [
                'unloadCityId',
                'exist',
                'targetClass' => City::className(),
                'targetAttribute' => 'id',
                'message' => Yii::t('app', 'LOAD_CITY_MY_LOADS_FILTER_IS_NOT_EXIST'),
                'on' => [
                    self::SCENARIO_SEARCH_CLIENT,
                    self::SCENARIO_CLIENT_FILTERS_LOADS_SUGGESTIONS,
                    self::SCENARIO_ADMIN_FILTERS_LOADS,
                ]
            ],

            // My loads filter
            [
                'myLoadsFilter',
                'each',
                'rule' => [
                    'exist',
                    'targetClass' => City::className(),
                    'targetAttribute' => 'id',
                    'message' => Yii::t('app', 'LOAD_CITY_MY_LOADS_FILTER_IS_NOT_EXIST'),
                ]
            ],

            // Load country
            ['loadCountry', 'string', 'message' => Yii::t('app', 'LOAD_LOAD_COUNTRY_IS_NOT_STRING')],
            [
                'loadCountry',
                'in',
                'range' => array_keys(Country::getAssociativeNames()),
                'message' => Yii::t('app', 'LOAD_LOAD_COUNTRY_IS_NOT_IN_RANGE')
            ],

            // Unload country
            ['unloadCountry', 'string', 'message' => Yii::t('app', 'LOAD_UNLOAD_COUNTRY_IS_NOT_STRING')],
            [
                'unloadCountry',
                'in',
                'range' => array_keys(Country::getAssociativeNames()),
                'message' => Yii::t('app', 'LOAD_UNLOAD_COUNTRY_IS_NOT_IN_RANGE')
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'load_id' => Yii::t('app', 'LOAD_CITY_LOAD_ID_LABEL'),
            'city_id' => Yii::t('app', 'LOAD_CITY_CITY_ID_LABEL'),
            'load_postal_code' => Yii::t('app', 'LOAD_POSTAL_CODE_LABEL'),
            'unload_postal_code' => Yii::t('app', 'UNLOAD_POSTAL_CODE_LABEL'),
            'type' => Yii::t('app', 'LOAD_CITY_TYPE_LABEL'),
            'created_at' => Yii::t('app', 'LOAD_CITY_CREATED_AT_LABEL'),
            'updated_at' => Yii::t('app', 'LOAD_CITY_UPDATED_AT_LABEL'),
            'loadCityId' => Yii::t('app', 'LOAD_CITY_LOAD_CITY_ID_LABEL'),
            'unloadCityId' => Yii::t('app', 'LOAD_CITY_UNLOAD_CITY_ID_LABEL'),
            'myLoadsFilter' => Yii::t('app', 'LOAD_CITY_MY_LOADS_FILTER_LABEL'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLoad()
    {
        return $this->hasOne(Load::className(), ['id' => 'load_id']);
    }

    /**
     * Returns formatted countries and cities string
     *
     * @param array $countries List of countries with cities inside
     * @return string
     */
    public static function getFormattedCities($countries)
    {
        $string = '';
        foreach ($countries as $countryCode => $cities) {
            $string .= Html::beginTag('div');
            $string .= Icon::show(strtolower($countryCode), [], Icon::FI) . $countryCode . ', ';
            foreach ($cities as $cityName) {
                $string .= $cityName . ', ';
            }
            $string = rtrim($string, ', ');
            $string .= Html::endTag('div');
            $string .= (count($countries) > 1 ? Html::beginTag('span') . ' + ' . Html::endTag('span') : '');
        }
        return rtrim($string);
    }

    /**
     * Adds city name to country list
     *
     * @param array $cities List of load or unload cities
     */
    public function addCitiesToCountryList(&$cities = [])
    {
        if (array_key_exists($this->city->country_code, $cities)) {
            array_push($cities[$this->city->country_code], $this->city->name);
        } else {
            $cities[$this->city->country_code] = [$this->city->name];
        }
    }

    /**
     * Moves first load city and country to city list top
     *
     * @param array $cities List of load cities
     * @param LoadCity $firstLoadCity city that loads first
     */
    public static function orderByFirstLoadCity(&$cities, $firstLoadCity)
    {
        if (is_null($firstLoadCity)) {
            return;
        }

        $firstCountry = $firstLoadCity->city->country_code;
        $firstCity = $firstLoadCity->city->name;

        foreach ($cities as $countryKey => $countryCities) {
            if ($countryKey == $firstCountry) {
                $temp = [$countryKey => $countryCities];
                unset($cities[$countryKey]);
                $cities = $temp + $cities;

                foreach ($countryCities as $cityKey => $countryCity) {
                    if ($cityKey == $firstCity) {
                        $temp = [$countryCity];
                        unset($cities[$countryKey][$cityKey]);
                        $cities[$countryKey] = $temp + $cities[$countryKey];
                        return;
                    }
                }
            }
        }
    }

    /**
     * Inserts selected load and unload cities to database
     *
     * @param null|integer $loadId Load ID
     * @param array $selectedCities Selected load and unload cities
     * @return boolean|integer Number of rows affected by the execution or false if data is invalid
     */
    public function create($loadId = null, $selectedCities = [])
    {
        $cities = $this->getCitiesBatchInsert($selectedCities, $loadId);
        if ($cities) {
            return Yii::$app->db->createCommand()->batchInsert(self::tableName(), $this->attributes(),
                $cities)->execute();
        }
        return false;
    }

    /**
     * Returns list of generated load cities for batch insert
     *
     * @param array $selectedCities Selected load and unload cities
     * @param null|integer $loadId Load ID
     * @return array|boolean False if load or unload data is invalid
     */
    private function getCitiesBatchInsert($selectedCities = [], $loadId = null)
    {
        $load = $this->generateCitiesBatchInsert($selectedCities, $loadId, self::LOADING, 'loadCityId');
        $unload = $this->generateCitiesBatchInsert($selectedCities, $loadId, self::UNLOADING, 'unloadCityId');
        return ($load && $unload) ? array_merge($load, $unload) : false;
    }

    /**
     * Generates load or unload cities for batch insert
     *
     * @param array $selectedCities Selected load or unload cities
     * @param null|integer $loadId Load ID
     * @param null|integer $type City type
     * @param null|integer $attribute Load or unload city attribute name
     * @return array|boolean Generated load or unload cities or false if data is not valid
     */
    private function generateCitiesBatchInsert($selectedCities = [], $loadId = null, $type = null, $attribute = null)
    {
        if (!isset($selectedCities[$attribute]) || empty($selectedCities[$attribute])) {
            return false;
        }

        $cities = [];
        $this->scenario = self::SCENARIO_ANNOUNCE_SERVER;
        foreach ($selectedCities[$attribute] as $cityId) {
            $city = [
                'id' => null,
                'load_id' => $loadId,
                'city_id' => $cityId,
                'load_postal_code' => $selectedCities['load_postal_code'],
                'unload_postal_code' => $selectedCities['unload_postal_code'],
                'type' => $type,
                'created_at' => time(),
                'updated_at' => time(),
            ];
            $this->setAttributes($city);
            if (!$this->validate()) {
                $this->scenario = self::SCENARIO_ANNOUNCE_SERVER;
                return false;
            }
            array_push($cities, $city);
        }
        return $cities;
    }

    /**
     * Formats cities names
     *
     * @param array $ids Cities IDs
     * @return string
     */
    public function formatCitiesNames($ids = [])
    {
        $cities = City::findAllByIds($ids);
        $string = '';

        foreach ($cities as $city) {
            $string .= $city->name . ', ';
        }

        return rtrim($string, ', ');
    }

    /**
     * Returns formatted flag, country and city string for search results
     *
     * @param null|integer $type Load city type
     * @return string
     */
    public function getFormattedSearchCity($type = null)
    {
        $id = $type == self::LOADING ? $this->loadCityId : $this->unloadCityId;
        $city = City::findById($id);
        $string = Html::beginTag('span');
        $string .= Icon::show(strtolower($city->country_code), [], Icon::FI) . $city->country_code . ', ' . $city->name;
        $string .= Html::endTag('span');

        return $string;
    }

    /**
     * Returns formatted flag, country and city string for suggestions
     *
     * @param null|integer $cityId Search city id
     * @return string
     */
    public static function getFormattedSuggestionCity($cityId = null)
    {
        $city = City::findById($cityId);
        $string = Html::beginTag('span');
        $string .= Icon::show(strtolower($city->country_code), [], Icon::FI) . $city->country_code . ', ' . $city->name;
        $string .= Html::endTag('span');

        return $string;
    }

    /**
     * Checks whether load city is loading
     *
     * @return boolean
     */
    public function isLoadingCity()
    {
        return $this->type == self::LOADING;
    }

    /**
     * Checks whether load city is unloading
     *
     * @return boolean
     */
    public function isUnloadingCity()
    {
        return $this->type == self::UNLOADING;
    }

    /**
     * Finds and returns all or filtered loads
     *
     * @param null|City $loadCity City model or null if city not found
     * @param null|City $unloadCity City model or null if city not found
     * @param null|integer $type Type of filtered loads
     * @param null|integer|array $loadId Specific load ID
     * @return ActiveQuery
     */
    public static function getLoadsQuery($loadCity = null, $unloadCity = null, $type = null, $loadId = null)
    {
        $loadQuery = self::find()
            ->joinWith('load')
            ->joinWith('city')
            ->joinWith('load.user')
            ->where([
                Load::tableName() . '.status' => Load::ACTIVE,
                Load::tableName() . '.active' => Load::ACTIVATED,
                Load::tableName() . '.type' => is_null($type) ? [Load::TYPE_PARTIAL, Load::TYPE_FULL] : $type,
            ])
            ->andWhere([
                'or',
                [
                    User::tableName() . '.active' => User::ACTIVE,
                    User::tableName() . '.allow' => User::ALLOWED,
                    User::tableName() . '.archive' => User::NOT_ARCHIVED,
                ],
                [Load::tableName() . '.user_id' => null],
            ])
            ->andWhere(['>', Load::tableName() . '.date_of_expiry', time()])
            ->andFilterWhere([Load::tableName() . '.id' => $loadId]);

        if (!is_null($loadCity)) {
            if ($loadCity->isCountry()) {
                $loadQuery->andWhere([
                    'and',
                    [self::tableName() . '.type' => self::LOADING],
                    [City::tableName() . '.country_code' => $loadCity->country_code],
                ]);
            } else {
                $loadQuery->andWhere([
                    'and',
                    [self::tableName() . '.type' => self::LOADING],
                    [self::tableName() . '.city_id' => $loadCity->id],
                ]);
            }
        }

        $unloadQuery = self::find()
            ->joinWith('load')
            ->joinWith('city')
            ->joinWith('load.user')
            ->where([
                Load::tableName() . '.status' => Load::ACTIVE,
                Load::tableName() . '.active' => Load::ACTIVATED,
                Load::tableName() . '.type' => is_null($type) ? [Load::TYPE_PARTIAL, Load::TYPE_FULL] : $type,
            ])
            ->andWhere([
                'or',
                [
                    User::tableName() . '.active' => User::ACTIVE,
                    User::tableName() . '.allow' => User::ALLOWED,
                    User::tableName() . '.archive' => User::NOT_ARCHIVED,
                ],
                [Load::tableName() . '.user_id' => null],
            ])
            ->andWhere(['>', Load::tableName() . '.date_of_expiry', time()])
            ->andFilterWhere([Load::tableName() . '.id' => $loadId]);

        if (!is_null($unloadCity)) {
            if ($unloadCity->isCountry()) {
                $unloadQuery->andWhere([
                    'and',
                    [self::tableName() . '.type' => self::UNLOADING],
                    [City::tableName() . '.country_code' => $unloadCity->country_code],
                ]);
            } else {
                $unloadQuery->andWhere([
                    'and',
                    [self::tableName() . '.type' => self::UNLOADING],
                    [self::tableName() . '.city_id' => $unloadCity->id],
                ]);
            }
        }

        return self::find()->from(['loadQuery' => $loadQuery])
            ->join('JOIN', ['unloadQuery' => $unloadQuery], 'loadQuery.load_id = unloadQuery.load_id')
            ->joinWith('load', true, 'JOIN')
            ->groupBy('load_id')
            ->orderBy(['load.car_pos_adv' => SORT_DESC, 'load.created_at' => SORT_DESC]);
    }

    /**
     * Finds and returns all or filtered (and active) loads [extended]
     *
     * @param array $params
     * May contain [loadCityId, unloadCityId, loadCountryId, unloadCountryId, searchRadius, type]
     * @return ActiveQuery
     */
    public static function getLoadsQueryExtended($params)
    {
        // Radius
        if (isset($params['searchRadius']) && in_array($params['searchRadius'],
                [Load::FIRST_RADIUS, Load::SECOND_RADIUS, Load::THIRD_RADIUS])) {
            $searchRadius = $params['searchRadius'];
        } else {
            $searchRadius = Load::FIRST_RADIUS;
        }
        // LOAD QUERY
        $loadQuery = self::find()
            ->joinWith('load')
            ->joinWith('city')
            ->joinWith('load.user')
            ->where([
                Load::tableName() . '.status' => Load::ACTIVE,
                Load::tableName() . '.active' => Load::ACTIVATED
            ])
            ->andWhere([
                'or',
                [
                    User::tableName() . '.active' => User::ACTIVE,
                    User::tableName() . '.allow' => User::ALLOWED,
                    User::tableName() . '.archive' => User::NOT_ARCHIVED,
                ],
                [Load::tableName() . '.user_id' => null],
            ])
            ->andWhere(['>', Load::tableName() . '.date_of_expiry', time()]);
        // Type
        if (isset($params['type']) && $params['type'] !== '' && in_array($params['type'],
                [Load::TYPE_FULL, Load::TYPE_PARTIAL])) {
            $loadQuery->andWhere([
                Load::tableName() . '.type' => $params['type'],
            ]);
        }
        // Load cities in search radius
        if (isset($params['loadCityId'])) {
            $loadCity = City::findOne($params['loadCityId']);
            if ($loadCity instanceof City) {
                if ($loadCity->isCountry()) {
                    $params['loadCountryId'] = $params['loadCityId'];
                } else {
                    $citiesInRadius = array_column(Cities::getCitiesInArea($loadCity, $searchRadius), '_id');
                    if (!empty($citiesInRadius)) {
                        $loadQuery->andWhere([
                            self::tableName() . '.type' => self::LOADING,
                            self::tableName() . '.city_id' => $citiesInRadius
                        ]);
                    }
                }
            }
        }
        // Load Country
        if (isset($params['loadCountryId'])) {
            $loadCountry = City::findOne($params['loadCountryId']);
            if ($loadCountry instanceof City && $loadCountry->isCountry()) {
                $loadQuery->andWhere([
                    self::tableName() . '.type' => self::LOADING,
                    City::tableName() . '.country_code' => $loadCountry->country_code
                ]);
            }
        }
        // UNLOAD QUERY
        $unloadQuery = self::find()
            ->joinWith('load')
            ->joinWith('city')
            ->joinWith('load.user')
            ->where([
                Load::tableName() . '.status' => Load::ACTIVE,
                Load::tableName() . '.active' => Load::ACTIVATED
            ])
            ->andWhere([
                'or',
                [
                    User::tableName() . '.active' => User::ACTIVE,
                    User::tableName() . '.allow' => User::ALLOWED,
                    User::tableName() . '.archive' => User::NOT_ARCHIVED,
                ],
                [Load::tableName() . '.user_id' => null],
            ])
            ->andWhere(['>', Load::tableName() . '.date_of_expiry', time()]);
        // Type
        if (isset($params['type']) && in_array($params['type'], [Load::TYPE_FULL, Load::TYPE_PARTIAL])) {
            $unloadQuery->andWhere([
                Load::tableName() . '.type' => $params['type'],
            ]);
        }
        // Unload cities in search radius
        if (isset($params['unloadCityId'])) {
            $unloadCity = City::findOne($params['unloadCityId']);
            if ($unloadCity instanceof City) {
                if ($unloadCity->isCountry()) {
                    $params['unloadCountryId'] = $params['unloadCityId'];
                } else {
                    $citiesInRadius = array_column(Cities::getCitiesInArea($unloadCity, $searchRadius), '_id');
                    if (!empty($citiesInRadius)) {
                        $unloadQuery->andWhere([
                            self::tableName() . '.type' => self::UNLOADING,
                            self::tableName() . '.city_id' => $citiesInRadius
                        ]);
                    }
                }
            }
        }
        // Unload Country
        if (isset($params['unloadCountryId'])) {
            $unloadCountry = City::findOne($params['unloadCountryId']);
            if ($unloadCountry instanceof City && $unloadCountry->isCountry()) {
                $unloadQuery->andWhere([
                    self::tableName() . '.type' => self::UNLOADING,
                    City::tableName() . '.country_code' => $unloadCountry->country_code
                ]);
            }
        }

        return self::find()->from(['loadQuery' => $loadQuery])
            ->join('JOIN', ['unloadQuery' => $unloadQuery], 'loadQuery.load_id = unloadQuery.load_id')
            ->joinWith('load', true, 'JOIN')
            ->groupBy('load_id')
            ->orderBy(['load.car_pos_adv' => SORT_DESC, 'load.created_at' => SORT_DESC]);
    }

    /**
     * Returns list of unactive/expired
     *
     * @param array $params
     * May contain [loadCityId, unloadCityId, loadCountryId, unloadCountryId, searchRadius, type]
     * @return array
     */
    public static function getExpiredLoadsQueryExtended($params)
    {
        // Radius
        if (isset($params['searchRadius']) && in_array($params['searchRadius'],
                [Load::FIRST_RADIUS, Load::SECOND_RADIUS, Load::THIRD_RADIUS])) {
            $searchRadius = $params['searchRadius'];
        } else {
            $searchRadius = Load::FIRST_RADIUS;
        }
        // Load query
        $loadQuery = Load::find()->select(sprintf('%s.id', Load::tableName()))
            ->join('LEFT JOIN', User::tableName(), sprintf('%s.user_id=%s.id', Load::tableName(), User::tableName()))
            ->join('LEFT JOIN', LoadCity::tableName(),
                sprintf('%s.id=%s.load_id', Load::tableName(), LoadCity::tableName()))
            ->join('LEFT JOIN', City::tableName(),
                sprintf('%s.city_id=%s.id', LoadCity::tableName(), City::tableName()))
            ->where(
                ['<', sprintf('%s.date_of_expiry', Load::tableName()), time()]
            );
        $loadQuery->andWhere([
            sprintf('%s.active', User::tableName()) => User::ACTIVE,
            sprintf('%s.allow', User::tableName()) => User::ALLOWED,
            sprintf('%s.archive', User::tableName()) => User::NOT_ARCHIVED
        ]);
        // Type
        if (isset($params['type']) && $params['type'] !== '' && in_array($params['type'],
                [Load::TYPE_FULL, Load::TYPE_PARTIAL])) {
            $loadQuery->andWhere([
                sprintf('%s.type', Load::tableName()) => $params['type'],
            ]);
        }
        $unloadQuery = clone $loadQuery;
        // LoadCity
        if (isset($params['loadCityId'])) {
            $loadCity = City::findOne($params['loadCityId']);
            if ($loadCity instanceof City) {
                if ($loadCity->isCountry()) {
                    $params['loadCountryId'] = $params['loadCityId'];
                } else {
                    $citiesInRadius = array_column(Cities::getCitiesInArea($loadCity, $searchRadius), '_id');
                    if (!empty($citiesInRadius)) {
                        $loadQuery->andWhere([
                            sprintf('%s.type', self::tableName()) => self::LOADING,
                            sprintf('%s.city_id', self::tableName()) => $citiesInRadius
                        ]);
                    }
                }
            }
        }
        // Load Country
        if (isset($params['loadCountryId'])) {
            $loadCountry = City::findOne($params['loadCountryId']);
            if ($loadCountry instanceof City && $loadCountry->isCountry()) {
                $loadQuery->andWhere([
                    sprintf('%s.type', self::tableName()) => self::LOADING,
                    sprintf('%s.country_code', City::tableName()) => $loadCountry->country_code
                ]);
            }
        }
        // Unload query
        if (isset($params['unloadCityId'])) {
            $unloadCity = City::findOne($params['unloadCityId']);
            if ($unloadCity instanceof City) {
                if ($unloadCity->isCountry()) {
                    $params['unloadCountryId'] = $params['unloadCityId'];
                } else {
                    $citiesInRadius = array_column(Cities::getCitiesInArea($unloadCity, $searchRadius), '_id');
                    if (!empty($citiesInRadius)) {
                        $unloadQuery->andWhere([
                            sprintf('%s.type', self::tableName()) => self::UNLOADING,
                            sprintf('%s.city_id', self::tableName()) => $citiesInRadius
                        ]);
                    }
                }
            }
        }
        // Unload Country
        if (isset($params['unloadCountryId'])) {
            $unloadCountry = City::findOne($params['unloadCountryId']);
            if ($unloadCountry instanceof City && $unloadCountry->isCountry()) {
                $unloadQuery->andWhere([
                    sprintf('%s.type', self::tableName()) => self::UNLOADING,
                    sprintf('%s.country_code', City::tableName()) => $unloadCountry->country_code
                ]);
            }
        }

        return Load::find()
            ->indexBy('load_id')
            ->select([
                'user_id',
                'load_id' => 'id',
                'times_listed' => 'count(*)'
            ])
            ->where(['id' => $loadQuery])->andWhere(['id' => $unloadQuery])
            ->groupBy(['user_id'])->orderBy(['times_listed' => SORT_DESC])->asArray()->all();
    }

    /**
     * Divides load cities to partial and full load cities
     *
     * @param array $loadCities List of load cities
     * @return array
     */
    public static function divideCities($loadCities = [])
    {
        $partialCities = [];
        $fullCities = [];

        /** @var self $loadCity */
        foreach ($loadCities as $loadCity) {
            if ($loadCity->load->isTypePartial()) {
                self::addCities($loadCity, $partialCities);
            } else {
                self::addCities($loadCity, $fullCities);
            }
        }

        return [$partialCities, $fullCities];
    }

    /**
     * Adds cities to cities container
     *
     * @param LoadCity $loadCity Load city model
     * @param array $cities Cities container
     */
    public static function addCities(self $loadCity, &$cities)
    {
        $coordinates = $loadCity->city->latitude . ', ' . $loadCity->city->longitude;

        if (array_key_exists($coordinates, $cities)) {
            array_push($cities[$coordinates], $loadCity->load);
        } else {
            $cities[$coordinates] = [$loadCity->load];
        }

        return;
    }

    /**
     * Formats country code and cities names
     *
     * @param array $items List of country codes with cities names
     * @return string
     */
    public static function formatCountryCodeAndCitiesNames($items)
    {
        $flagWithCitiesNames = '';
        foreach ($items as $countryCode => $cities) {
            $flag = Html::tag('i', '', ['class' => 'flag-icon flag-icon-' . strtolower($countryCode)]);
            $names = self::convertCitiesNamesToString($cities);
            $flagWithCitiesNames .= "$flag $countryCode, " . addslashes($names) . " + ";
        }

        return rtrim($flagWithCitiesNames, ' + ');
    }

    /**
     * Converts cities names from array to string
     *
     * @param array $citiesNames List of cities names
     * @return string
     */
    public static function convertCitiesNamesToString($citiesNames)
    {
        $names = '';
        foreach ($citiesNames as $cityName) {
            $names .= $cityName . ', ';
        }

        return rtrim($names, ', ');
    }

    /**
     * Finds and returns all or filtered loads
     *
     * @param null|City $city City model or null if city not found
     * @param null|integer $cityId ID of filtered city
     * @param null|integer $type Type of filtered loads
     * @param null|integer|array $loadId Specific load ID
     * @return ActiveQuery
     */
    public static function getLoadsSuggestions($loadId)
    {
        return self::find()
            ->joinWith('load')
            ->joinWith('load.user')
            ->where([
                Load::tableName() . '.id' => $loadId,
                Load::tableName() . '.status' => Load::ACTIVE,
                Load::tableName() . '.active' => Load::ACTIVATED,
            ])
            ->andWhere([
                'or',
                [
                    User::tableName() . '.active' => User::ACTIVE,
                    User::tableName() . '.allow' => User::ALLOWED,
                    User::tableName() . '.archive' => User::NOT_ARCHIVED,
                ],
                [Load::tableName() . '.user_id' => null],
            ])
            ->andWhere(['>', Load::tableName() . '.date_of_expiry', time()])
            ->groupBy(self::tableName() . '.load_id');
    }
}
