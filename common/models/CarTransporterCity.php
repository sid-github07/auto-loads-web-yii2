<?php

namespace common\models;

use common\components\ElasticSearch\Cities;
use kartik\icons\Icon;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%car_transporter_city}}".
 *
 * @property integer $id
 * @property integer $car_transporter_id
 * @property integer $city_id
 * @property string $country_code
 * @property integer $type
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Country $countryCode
 * @property CarTransporter $carTransporter
 * @property City $city
 */
class CarTransporterCity extends ActiveRecord
{
    const COUNTRY_CODE_MAX_LENGTH = 255;

    const TYPE_LOAD = 0;
    const TYPE_UNLOAD = 1;
    
    /** @const integer max lenght for load and unload city postal code */
    const POSTAL_CODE_MAX_LENGTH = 8;

    const SCENARIO_USER_CREATES_NEW_ANNOUNCEMENT = 'user-creates-new-announcement';
    const SCENARIO_USER_SEARCHES_FOR_CAR_TRANSPORTER = 'user-searches-for-car-transporter';
    const SCENARIO_USER_FILTERS_MY_CAR_TRANSPORTERS = 'user-filters-my-car-transporters';
    const SCENARIO_ADMIN_FILTERS_CAR_TRANSPORTERS = 'admin-filters-car-transporters';
    const SCENARIO_SYSTEM_MIGRATES_CAR_TRANSPORTER_CITY = 'system-migrates-car-transporter-city';

    public $loadLocations;
    public $unloadLocations;

    public $loadLocation;
    public $unloadLocation;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%car_transporter_city}}';
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
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_USER_CREATES_NEW_ANNOUNCEMENT] = [
            'loadLocations',
            'unloadLocations',
            'load_postal_code',
            'unload_postal_code',
        ];
        $scenarios[self::SCENARIO_USER_SEARCHES_FOR_CAR_TRANSPORTER] = [
            'loadLocation',
            'unloadLocation',
        ];
        $scenarios[self::SCENARIO_USER_FILTERS_MY_CAR_TRANSPORTERS] = [
            'loadLocations',
        ];
        $scenarios[self::SCENARIO_ADMIN_FILTERS_CAR_TRANSPORTERS] = [
            'loadLocation',
            'unloadLocation',
        ];
        $scenarios[self::SCENARIO_SYSTEM_MIGRATES_CAR_TRANSPORTER_CITY] = [
            'id',
            'car_transporter_id',
            'city_id',
            'country_code',
            'type',
            'created_at',
            'updated_at',
        ];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Car transporter ID
            ['car_transporter_id', 'required'],
            ['car_transporter_id', 'integer'],
            ['car_transporter_id', 'exist',
                'targetClass' => CarTransporter::className(),
                'targetAttribute' => ['car_transporter_id' => 'id']],

            // City ID
            ['city_id', 'required'],
            ['city_id', 'integer'],
            ['city_id', 'exist',
                'targetClass' => City::className(),
                'targetAttribute' => ['city_id' => 'id']],
            
            // Load Postal Code
            ['load_postal_code', 'string', 'max' => self::POSTAL_CODE_MAX_LENGTH,
                            'tooLong' => Yii::t('app', 'POSTAL_CODE_IS_TOO_LONG', [
                                'length' => self::POSTAL_CODE_MAX_LENGTH,
                            ]),
                            'message' => Yii::t('app', 'LOAD_TOKEN_IS_NOT_STRING')],
            
            // Unload Postal Code
            ['unload_postal_code', 'string', 'max' => self::POSTAL_CODE_MAX_LENGTH,
                            'tooLong' => Yii::t('app', 'POSTAL_CODE_IS_TOO_LONG', [
                                'length' => self::POSTAL_CODE_MAX_LENGTH,
                            ]),
                            'message' => Yii::t('app', 'POSTAL_CODE_IS_NOT_STRING')],

            // Country code
            ['country_code', 'required'],
            ['country_code', 'string', 'max' => self::COUNTRY_CODE_MAX_LENGTH],
            ['country_code', 'exist',
                'targetClass' => Country::className(),
                'targetAttribute' => ['country_code' => 'code']],

            // Type
            ['type', 'required'],
            ['type', 'integer'],
            ['type', 'in', 'range' => [self::TYPE_LOAD, self::TYPE_UNLOAD]],

            // Created at
            ['created_at', 'integer'],

            // Updated at
            ['updated_at', 'integer'],

            // Load locations
            ['loadLocations', 'required',
                'message' => Yii::t('app', 'LOAD_LOCATIONS_CANNOT_BE_EMPTY'),
                'on' => self::SCENARIO_USER_CREATES_NEW_ANNOUNCEMENT,
                'except' => self::SCENARIO_USER_FILTERS_MY_CAR_TRANSPORTERS],
            ['loadLocations', 'each', 'rule' => ['exist', 'targetClass' => City::className(), 'targetAttribute' => 'id']],

            // Unload locations
            ['unloadLocations', 'required',
                'message' => Yii::t('app', 'UNLOAD_LOCATIONS_CANNOT_BE_EMPTY'),
                'on' => self::SCENARIO_USER_CREATES_NEW_ANNOUNCEMENT],
            ['unloadLocations', 'each', 'rule' => ['exist', 'targetClass' => City::className(), 'targetAttribute' => 'id']],

            // Load location
            ['loadLocation', 'required',
                'message' => Yii::t('app', 'LOAD_LOCATIONS_CANNOT_BE_EMPTY'),
                'on' => self::SCENARIO_USER_SEARCHES_FOR_CAR_TRANSPORTER,
                'except' => self::SCENARIO_ADMIN_FILTERS_CAR_TRANSPORTERS],
            ['loadLocation', 'integer'],
            ['loadLocation', 'exist', 'targetClass' => City::className(), 'targetAttribute' => 'id'],

            // Unload location
            ['unloadLocation', 'required',
                'message' => Yii::t('app', 'UNLOAD_LOCATIONS_CANNOT_BE_EMPTY'),
                'on' => self::SCENARIO_USER_SEARCHES_FOR_CAR_TRANSPORTER,
                'except' => self::SCENARIO_ADMIN_FILTERS_CAR_TRANSPORTERS],
            ['unloadLocation', 'integer'],
            ['unloadLocation', 'exist', 'targetClass' => City::className(), 'targetAttribute' => 'id'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'car_transporter_id' => 'Car Transporter ID',
            'city_id' => 'City ID',
            'load_postal_code' => Yii::t('app', 'LOAD_POSTAL_CODE_LABEL'),
            'unload_postal_code' => Yii::t('app', 'UNLOAD_POSTAL_CODE_LABEL'),
            'country_code' => 'Country Code',
            'type' => 'Type',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCountryCode()
    {
        return $this->hasOne(Country::className(), ['code' => 'country_code']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCarTransporter()
    {
        return $this->hasOne(CarTransporter::className(), ['id' => 'car_transporter_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    /**
     * Associates load or unload locations IDs with its name
     *
     * @param string $attribute "loadLocations" or "unloadLocations"
     */
    public function associateLocationIdWithName($attribute)
    {
        $cities = City::findAll($this->$attribute);
        $this->$attribute = ArrayHelper::map($cities, 'id', function (City $city) {
            return $city->getNameAndCountryCode();
        });
    }

    /**
     * Finds and returns all active and activated car transporters or only filtered ones
     *
     * @param null|City $loadCity City model or null if city not found
     * @param null|City $unloadCity City model or null if city not found
     * @param null|integer $carTransporterId Specific car transporter ID
     * @return ActiveQuery
     */
    public static function getQuery($loadCity = null, $unloadCity = null, $carTransporterId = null)
    {
        $loadQuery = self::find()
            ->joinWith('carTransporter')
            ->joinWith('city')
            ->joinWith('carTransporter.user')
            ->where([
                CarTransporter::tableName() . '.visible' => CarTransporter::VISIBLE,
                CarTransporter::tableName() . '.archived' => CarTransporter::NOT_ARCHIVED,
            ])
            ->andWhere(['>', CarTransporter::tableName() . '.date_of_expiry', time()])
            ->andFilterWhere([CarTransporter::tableName() . '.id' => $carTransporterId])
            ->andWhere([
                User::tableName() . '.active' => User::ACTIVE,
                User::tableName() . '.allow' => User::ALLOWED,
                User::tableName() . '.archive' => User::NOT_ARCHIVED,
            ]);

        if (!is_null($loadCity)) {
            if ($loadCity->isCountry()) {
                $loadQuery->andWhere(['and',
                    [self::tableName() . '.type' => self::TYPE_LOAD],
                    [City::tableName() . '.country_code' => $loadCity->country_code],
                ]);
            } else {
                $loadQuery->andWhere(['and',
                    [self::tableName() . '.type' => self::TYPE_LOAD],
                    [self::tableName() . '.city_id' => $loadCity->id],
                ]);
            }
        }

        $unloadQuery = self::find()
            ->joinWith('carTransporter')
            ->joinWith('city')
            ->joinWith('carTransporter.user')
            ->where([
                CarTransporter::tableName() . '.visible' => CarTransporter::VISIBLE,
                CarTransporter::tableName() . '.archived' => CarTransporter::NOT_ARCHIVED,
            ])
            ->andWhere(['>', CarTransporter::tableName() . '.date_of_expiry', time()])
            ->andFilterWhere([CarTransporter::tableName() . '.id' => $carTransporterId])
            ->andWhere([
                User::tableName() . '.active' => User::ACTIVE,
                User::tableName() . '.allow' => User::ALLOWED,
                User::tableName() . '.archive' => User::NOT_ARCHIVED,
            ]);

        if (!is_null($unloadCity)) {
            if ($unloadCity->isCountry()) {
                $unloadQuery->andWhere(['and',
                    [self::tableName() . '.type' => self::TYPE_UNLOAD],
                    [City::tableName() . '.country_code' => $unloadCity->country_code],
                ]);
            } else {
                $unloadQuery->andWhere(['and',
                    [self::tableName() . '.type' => self::TYPE_UNLOAD],
                    [self::tableName() . '.city_id' => $unloadCity->id],
                ]);
            }
        }

        return self::find()->from(['loadQuery' => $loadQuery])
            ->join('JOIN', ['unloadQuery' => $unloadQuery], 'loadQuery.car_transporter_id = unloadQuery.car_transporter_id')
            ->joinWith('carTransporter', true, 'JOIN')
            ->groupBy('car_transporter_id')
            ->orderBy(['car_transporter.car_pos_adv' => SORT_DESC, 'car_transporter.created_at' => SORT_DESC]);
    }

    /**
     * Finds and returns all active and activated car transporters or only filtered ones
     *
     * @param array $params
     * May contain [loadCityId, unloadCityId, loadCountryId, unloadCountryId, searchRadius]
     * @return ActiveQuery
     */
    public static function getExtendedQuery($params)
    {
        // Radius
        if (isset($params['searchRadius']) && in_array($params['searchRadius'], [Load::FIRST_RADIUS, Load::SECOND_RADIUS, Load::THIRD_RADIUS])) {
            $searchRadius = $params['searchRadius'];
        } else {
            $searchRadius = Load::FIRST_RADIUS;
        }
        // LOAD QUERY
        $loadQuery = self::find()
            ->joinWith('carTransporter')
            ->joinWith('city')
            ->joinWith('carTransporter.user')
            ->where([
                CarTransporter::tableName() . '.visible' => CarTransporter::VISIBLE,
                CarTransporter::tableName() . '.archived' => CarTransporter::NOT_ARCHIVED,
            ])
            ->andWhere(['>', CarTransporter::tableName() . '.date_of_expiry', time()])
            ->andWhere([
                User::tableName() . '.active' => User::ACTIVE,
                User::tableName() . '.allow' => User::ALLOWED,
                User::tableName() . '.archive' => User::NOT_ARCHIVED,
            ]);

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
                            self::tableName() . '.type' => self::TYPE_LOAD,
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
                    self::tableName() . '.type' => self::TYPE_LOAD,
                    City::tableName() . '.country_code' => $loadCountry->country_code
                ]);
            }
        }
        // UNLOAD QUERY
        $unloadQuery = self::find()
            ->joinWith('carTransporter')
            ->joinWith('city')
            ->joinWith('carTransporter.user')
            ->where([
                CarTransporter::tableName() . '.visible' => CarTransporter::VISIBLE,
                CarTransporter::tableName() . '.archived' => CarTransporter::NOT_ARCHIVED,
            ])
            ->andWhere(['>', CarTransporter::tableName() . '.date_of_expiry', time()])
            ->andWhere([
                User::tableName() . '.active' => User::ACTIVE,
                User::tableName() . '.allow' => User::ALLOWED,
                User::tableName() . '.archive' => User::NOT_ARCHIVED,
            ]);
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
                            self::tableName() . '.type' => self::TYPE_UNLOAD,
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
                    self::tableName() . '.type' => self::TYPE_UNLOAD,
                    City::tableName() . '.country_code' => $unloadCountry->country_code
                ]);
            }
        }

        return self::find()->from(['loadQuery' => $loadQuery])
            ->join('JOIN', ['unloadQuery' => $unloadQuery], 'loadQuery.car_transporter_id = unloadQuery.car_transporter_id')
            ->joinWith('carTransporter', true, 'JOIN')
            ->groupBy('car_transporter_id')
            ->orderBy(['car_transporter.car_pos_adv' => SORT_DESC, 'car_transporter.created_at' => SORT_DESC]);
    }

    /**
     * Formats car transporter cities for popover
     *
     * @param array $items List of car transporter cities grouped by country
     * @return string
     */
    public static function formatPopoverCities($items)
    {
        $string = '';

        foreach ($items as $countryCode => $cities) {
            $flag = Html::tag('i', '', ['class' => 'flag-icon flag-icon-' . strtolower($countryCode)]);
            $names = City::combineNamesToString($cities);
            $string .= "$flag $countryCode, $names + ";
        }

        return rtrim($string, ' + ');
    }

    /**
     * Formats car transporter cities for table view
     *
     * @param integer $type Car transporter city type
     * @return string
     */
    public function formatTableCities($type)
    {
        $countryCities = $this->collectCountryCities($type);
        $string = '';

        foreach ($countryCities as $countryCode => $cities) {
            $icon = Icon::show(strtolower($countryCode), [], Icon::FI) . $countryCode . ', ';
            $citiesNames = City::combineNamesToString($cities);
            $div = Html::tag('div', $icon . $citiesNames);
            $plus = (count($countryCities) > 1 ? Html::tag('span', '+') : '');
            $string .= $div . $plus;
        }
        
        return rtrim($string, '<span>+</span>');
    }

    /**
     * Collects cities to countries array, where array key is country code and value is country cities
     *
     * @param integer $type Car transporter city type
     * @return array
     */
    private function collectCountryCities($type)
    {
        $countryCities = [];

        foreach ($this->carTransporter->carTransporterCities as $carTransporterCity) {
            if ($carTransporterCity->type == $type) {
                $this->addCityToCountriesList($carTransporterCity, $countryCities);
            }
        }

        return $countryCities;
    }

    /**
     * Adds city to country list
     *
     * @param CarTransporterCity $carTransporterCity Car transporter city model
     * @param array $countryCities List of country cities
     */
    private function addCityToCountriesList($carTransporterCity, &$countryCities)
    {
        if (array_key_exists($carTransporterCity->city->country_code, $countryCities)) {
            array_push($countryCities[$carTransporterCity->city->country_code], $carTransporterCity->city->name);
        } else {
            $countryCities[$carTransporterCity->city->country_code] = [$carTransporterCity->city->name];
        }
    }
}
