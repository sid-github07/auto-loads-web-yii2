<?php

namespace common\models;

use kartik\icons\Icon;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "{{%city}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $ansi_name
 * @property string $alt_name
 * @property string $latitude
 * @property string $longitude
 * @property string $country_code
 * @property integer $population
 * @property integer $elevation
 * @property string $timezone
 * @property string $modification_date
 *
 * @property CarTransporterCity[] $carTransporterCities
 * @property Company[] $companies
 * @property LoadCity[] $loadCities
 * @property User[] $users
 * @property UserInvoice[] $userInvoices
 * @property Country $country
 */
class City extends ActiveRecord
{
    /** @const string Default coordinates for loads map */
    const COORDINATES_CENTER_OF_EUROPE = '48.771635, 13.839543';

    private static $countriesList;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%city}}';
    }

    /**
     * @todo sutvarkyti taisykles
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'ansi_name', 'alt_name'], 'required'],
            [['alt_name'], 'string'],
            [['latitude', 'longitude'], 'number'],
            [['population', 'elevation'], 'integer'],
            [['modification_date'], 'safe'],
            [['name', 'ansi_name', 'timezone'], 'string', 'max' => 255],
            [['country_code'], 'string', 'max' => 2],
        ];
    }

    /**
     * @todo sutvarkyti label'ius
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'ansi_name' => Yii::t('app', 'Ansi Name'),
            'alt_name' => Yii::t('app', 'Alt Name'),
            'latitude' => Yii::t('app', 'Latitude'),
            'longitude' => Yii::t('app', 'Longitude'),
            'country_code' => Yii::t('app', 'Country Code'),
            'population' => Yii::t('app', 'Population'),
            'elevation' => Yii::t('app', 'Elevation'),
            'timezone' => Yii::t('app', 'Timezone'),
            'modification_date' => Yii::t('app', 'Modification Date'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCarTransporterCities()
    {
        return $this->hasMany(CarTransporterCity::className(), ['city_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCompanies()
    {
        return $this->hasMany(Company::className(), ['city_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLoadCities()
    {
        return $this->hasMany(LoadCity::className(), ['city_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['city_id' => 'id']);
    }
    
    /**
     * @return ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['code' => 'country_code']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUserInvoices()
    {
        return $this->hasMany(UserInvoice::className(), ['buyer_city_id' => 'id']);
    }

    /**
     * Finds city by ID
     *
     * @param null|integer $id City ID that model needs to be loaded
     * @return null|static
     * @throws NotFoundHttpException If city model not found
     */
    public static function findById($id = null)
    {
        $city = self::findOne($id);
        if (is_null($city)) {
            throw new NotFoundHttpException(Yii::t('alert', 'CITY_NOT_FOUND_BY_ID'));
        }
        return $city;
    }

    /**
     * Finds cities by given cities IDs
     *
     * @param array $ids List of cities IDs
     * @param boolean $asArray Whether result must be returned as array
     * @param boolean $throwException Attribute, whether throw exception if cities not found
     * @return static[]
     * @throws NotFoundHttpException If cities not found by given list of cities IDs
     */
    public static function findAllByIds($ids = [], $asArray = false, $throwException = true)
    {
        $cities = self::find()->where(['id' => $ids])->asArray($asArray)->all();

        if (empty($cities) && $throwException) {
            throw new NotFoundHttpException(Yii::t('alert', 'CITIES_NOT_FOUND_BY_IDS'));
        }

        return $cities;
    }

    /**
     * Returns city name with country code in brackets by given city ID
     *
     * @param null|integer $id City ID
     * @return string
     */
    public static function getNameById($id = null)
    {
        if (is_null($id) || empty($id)) {
            return '';
        }
        $city = self::findById($id);
        return "$city->name ($city->country_code)";
    }

    /**
     * Searches for all country cities and returns its IDs
     *
     * @param string $countryCode Country code
     * @return array List of country cities IDs
     */
    public static function findCountryCitiesIds($countryCode)
    {
        return self::find()->select('id')->where(['country_code' => $countryCode])->column();
    }

    /**
     * Checks whether city is actually a country
     *
     * @return boolean
     */
    public function isCountry()
    {
        return is_null($this->modification_date);
    }

    /**
     * Finds and returns list of countries country code by list of cities IDs or specific city ID
     *
     * @param integer|array $id List of cities IDs or specific city ID
     * @return array
     */
    public static function findCountriesCountryCode($id)
    {
        $modification_date = null;
        return self::find()->select('country_code')->where(compact('id', 'modification_date'))->column();
    }

    /**
     * Returns current city coordinates in array
     *
     * @return array
     */
    public function getCoordinates()
    {
        return [$this->latitude, $this->longitude];
    }

    /**
     * Returns current city name with country code in brackets
     *
     * @return string
     */
    public function getNameAndCountryCode()
    {
        return Yii::t('country', $this->name) . ' (' . $this->country_code . ')';
    }

    /**
     * Combines given cities names to one string separated by commas
     *
     * @param array $cities List of cities
     * @return string
     */
    public static function combineNamesToString($cities)
    {
        $names = '';
        foreach ($cities as $city) {
            $names .= (is_string($city) ? addSlashes($city) : addSlashes($city->name)) . ', ';
        }
        return rtrim($names, ', ');
    }

    /**
     * Returns formatted search location
     *
     * @return string
     */
    public function getSearchLocation()
    {
        $icon = Icon::show(strtolower($this->country_code), [], Icon::FI);
        $codeAndName = $this->country_code . ', ' . $this->name;
        return $icon . $codeAndName;
    }

    /**
     * Finds and returns country
     *
     * @param string $countryCode Searchable country code
     * @return static
     * @throws NotFoundHttpException If country not found
     */
    public static function findCountry($countryCode)
    {
        $country = self::findOne(['country_code' => $countryCode, 'modification_date' => null]);

        if (is_null($country)) {
            throw new NotFoundHttpException(Yii::t('alert', 'COUNTRY_NOT_FOUND'));
        }

        return $country;
    }

    /**
     * @return array
     */
    public static function getOriginalCountriesList(){
        if (is_null(self::$countriesList)) {
            if (!Yii::$app->cache->exists('countriesList')) {
                $list = self::find()->where(['modification_date' => null])->indexBy('country_code')->asArray()->all();
                Yii::$app->cache->set('countriesList', $list, 24*60*60); // 1 day
            }
            self::$countriesList = Yii::$app->cache->get('countriesList');
        }
        return self::$countriesList;
    }

    /**
     * Returns associated list of countries
     *
     * @return array
     */
    public static function getCountries()
    {
        $countries = [];
        foreach (self::getOriginalCountriesList() as $country) {
            $countries[$country['id']] = sprintf('%s (%s)', Yii::t('country', $country['name']), $country['country_code']);
        }
        asort($countries, SORT_LOCALE_STRING);
        return $countries;
    }
	
	/**
     * Returns associated list of countries
     *
     * @return array
     */
    public static function getCountryCodes()
    {
        $countries = [];
        foreach (self::getOriginalCountriesList() as $country) {
            $countries[$country['country_code']] = sprintf('%s (%s)', Yii::t('country', $country['name']), $country['country_code']);
        }
        asort($countries, SORT_LOCALE_STRING);
        return $countries;
    }
}
