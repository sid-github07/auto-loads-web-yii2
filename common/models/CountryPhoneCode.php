<?php

namespace common\models;

use common\components\Languages;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%country_phone_code}}".
 *
 * @property integer $id
 * @property string $country_code
 * @property string $name
 * @property string $number
 */
class CountryPhoneCode extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%country_phone_code}}';
    }

    /**
     * @todo sutvarkyti taisykles
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['country_code', 'name', 'number'], 'required'],
            [['country_code'], 'string', 'max' => 2],
            [['name', 'number'], 'string', 'max' => 255],
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
            'country_code' => Yii::t('app', 'Country Code'),
            'name' => Yii::t('app', 'Name'),
            'number' => Yii::t('app', 'Number'),
        ];
    }

    /**
     * Checks whether given phone number beginning is in list of country phone code numbers
     *
     * @param integer $number Phone number beginning
     * @return boolean
     */
    public static function isNumberValid($number)
    {
        if (is_null($number) || empty($number)) {
            return false;
        }

        $number = self::removePlusSymbol($number);
        $numbers = self::getAllNumbers();
        return in_array($number, $numbers);
    }

    /**
     * Removes "+" symbol from given phone number
     *
     * @param $number
     * @return string
     */
    public static function removePlusSymbol($number)
    {
        if (substr($number, 0, 1) === '+') {
            return substr($number, 1);
        }
        return $number;
    }

    /**
     * Returns list of all countries phone numbers
     *
     * @return array
     */
    public static function getAllNumbers()
    {
        return self::find()->select('number')->column();
    }

    /**
     * Returns list of available phone numbers
     *
     * @return array
     */
    public static function getPhoneNumbers()
    {
        $array = [];
        $entries = self::find()->all();
        /** @var self[] $entries */
        foreach ($entries as $entry) {
            array_push($array, [
                'code' => strtolower($entry->country_code),
                'name' => $entry->name,
                'number' => '+' . $entry->number,
            ]);
        }
        return $array;
    }

    /**
     * Returns phone number and code by given country code or country number
     *
     * @param null|string $code Country code which number must be found
     * @param null|integer $number Country number which code must be found
     * @return array
     */
    public static function getActivePhoneNumber($code = null, $number = null)
    {
        if (is_null($code)) {
            $code = Languages::getCode();
        }

        if (is_null($number)) {
            $number = self::getNumberByCountryCode($code);
        } else {
            $code = self::getCountryCodeByNumber($number);
        }

        return [
            'code' => strtolower($code),
            'number' => '+' . $number,
        ];
    }

    /**
     * Returns phone number by given country code
     *
     * @param string $code Language name short version
     * @return false|null|string
     */
    public static function getNumberByCountryCode($code)
    {
        return self::find()->select('number')->where(['country_code' => strtoupper($code)])->scalar();
    }

    /**
     * Returns country code by given phone number beginning
     *
     * @param integer $number Phone number beginning
     * @return false|null|string
     */
    public static function getCountryCodeByNumber($number)
    {
        return self::find()->select('country_code')->where(['number' => $number])->scalar();
    }

    /**
     * Splits given phone number to country code and rest phone number
     *
     * @param string $number Phone number that should be split
     * @return array
     */
    public static function splitToCodeAndNumber($number = '')
    {
        $phoneNumbers = self::getPhoneNumbers();
        foreach ($phoneNumbers as $phoneNumber) {
            if (substr($number, 0, strlen($phoneNumber['number'])) === $phoneNumber['number']) {
                return ['code' => $phoneNumber['code'],'number' => $phoneNumber['number']];
            }
        }
        return ['code' => null, 'number' => null];
    }
}
