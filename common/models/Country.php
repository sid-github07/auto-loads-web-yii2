<?php

namespace common\models;

use common\components\Languages;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "{{%country}}".
 *
 * @property string $code
 * @property string $name
 * @property string $vat_rate
 */
class Country extends ActiveRecord
{
    /** @const integer Minimum and maximum length of country code */
    const CODE_LENGTH = 2;

    /** @const integer Minimum length of country name */
    const MIN_NAME_LENGTH = 2;

    /** @const integer Maximum length of country name */
    const MAX_NAME_LENGTH = 255;

    /** @const string Default language code */
    const DEFAULT_CODE = 'GB';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%country}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Code
            ['code', 'required', 'message' => Yii::t('app', 'COUNTRY_CODE_IS_REQUIRED')],
            ['code', 'string', 'min' => self::CODE_LENGTH,
                          'tooShort' => Yii::t('app', 'COUNTRY_CODE_IS_TOO_SHORT', [
                              'length' => self::CODE_LENGTH,
                          ]),
                               'max' => self::CODE_LENGTH,
                           'tooLong' => Yii::t('app', 'COUNTRY_CODE_IS_TOO_LONG', [
                               'length' => self::CODE_LENGTH
                           ])],
            ['code', 'unique', 'targetClass' => '\common\models\Country',
                                   'message' => Yii::t('app', 'COUNTRY_CODE_IS_NOT_UNIQUE')],
            ['code', 'filter', 'filter' => 'trim'],

            // Name
            ['name', 'required', 'message' => Yii::t('app', 'COUNTRY_NAME_IS_REQUIRED')],
            ['name', 'string', 'min' => self::MIN_NAME_LENGTH,
                          'tooShort' => Yii::t('app', 'COUNTRY_NAME_IS_TOO_SHORT', [
                              'length' => self::MIN_NAME_LENGTH,
                          ]),
                               'max' => self::MAX_NAME_LENGTH,
                           'tooLong' => Yii::t('app', 'COUNTRY_NAME_IS_TOO_LONG', [
                               'length' => self::MAX_NAME_LENGTH,
                           ])],
            ['name', 'filter', 'filter' => 'trim'],

            // VAT rate
            ['vat_rate', 'match', 'pattern' => '/^\d{1,3}(?:(\.|\,)\d{1,2})?$/',
                                  'message' => Yii::t('app', 'COUNTRY_VAT_RATE_IS_NOT_MATCH')],
            ['vat_rate', 'default', 'value' => null],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'code' => Yii::t('app', 'COUNTRY_LABEL_CODE'),
            'name' => Yii::t('app', 'COUNTRY_LABEL_NAME'),
            'vat_rate' => Yii::t('app', 'COUNTRY_LABEL_VAT_RATE'),
        ];
    }

    /**
     * Returns list of countries that have set VAT rate
     *
     * @return array
     */
    public static function getVatRateCountries()
    {
        return ArrayHelper::map(
            self::find()
                ->where('vat_rate IS NOT NULL')
                ->andWhere(['<>', 'vat_rate', '0.00'])
                ->all(),
            'code',
            'name'
        );
    }

    /**
     * Returns valid VAT rate country code
     *
     * @param null|string $countryCode VAT rate country code
     * @return null|string
     */
    public static function getValidVatRateCountryCode($countryCode = null)
    {
        if (is_null($countryCode)) {
            $countryCode = Languages::getCode();
        }

        if (strlen($countryCode) >= User::VAT_CODE_MIN_LENGTH) {
            $countryCode = substr($countryCode, 0, User::VAT_CODE_MIN_LENGTH);
        }

        if (self::isVatRateCountry($countryCode)) {
            return $countryCode;
        }

        // NOTE: not all website countries has VAT rate, that's why default code must be returned
        return self::DEFAULT_CODE;
    }

    /**
     * Checks whether given country code is between VAT rate countries list
     *
     * @param string $countryCode VAT rate country code
     * @return boolean
     */
    public static function isVatRateCountry($countryCode)
    {
        $vatRateCountries = self::getVatRateCountries();
        return array_key_exists(strtoupper($countryCode), $vatRateCountries);
    }

    /**
     * Returns current user company VAT rate
     *
     * @param null|integer $userId User ID
     *
     * @return false|null|string
     * @throws NotFoundHttpException If user company not found
     */
    public static function getUserVatRate($userId = null)
    {
        $userId = Yii::$app->user->isGuest ? $userId : Yii::$app->user->id;
        $company = Company::findUserCompany($userId);
        if (is_null($company)) {
            throw new NotFoundHttpException(Yii::t('alert', 'COMPANY_NOT_FOUND_BY_USER'));
        }

        $userCompanyCountryCode = $company->city->country_code;
        $country = self::findOne(['code' => strtoupper($userCompanyCountryCode)]);

        if ($country->isEUCountry()) {
            if ($country->isLithuanianCountry()) {
                return $country->vat_rate;
            } else {
                if (empty($company->vat_code)) {
                    return $country->vat_rate;
                } else {
                    return 0;
                }
            }
        } else {
            return 0;
        }
    }

    /**
     * Returns translated country name by given country code
     *
     * @param null|string $code Country code
     * @return false|null|string
     */
    public static function getNameByCode($code = null)
    {
        $name = self::find()->select('name')->where(['code' => $code])->scalar();
        return Yii::t('country', $name);
    }

    /**
     * Returns array of associative country names, where array key is country code and array value is country name
     *
     * @return array
     */
    public static function getAssociativeNames()
    {
        return ArrayHelper::map(self::find()->all(), 'code', 'name');
    }

    /**
     * Checks whether VAT must be applied to price
     *
     * @param integer $id Currently logged-in user ID
     * @return boolean
     */
    public static function applyVAT($id)
    {
        $company = Company::findOne(['owner_id' => $id]);
        if (is_null($company)) {
            $companyUser = CompanyUser::findOne(['user_id' => $id]);
            $company = is_null($companyUser) ? null : $companyUser->company;
        }

        if (is_null($company)) {
            return false;
        }

        $userCompanyCountryCode = $company->city->country_code;
        $country = self::findOne(['code' => strtoupper($userCompanyCountryCode)]);

        if ($country->isEUCountry()) {
            if ($country->isLithuanianCountry()) {
                return $country->vat_rate;
            } else {
                if (empty($company->vat_code)) {
                    return $country->vat_rate;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * Checks whether current country belongs to EU
     *
     * @return boolean
     */
    private function isEUCountry()
    {
        return !is_null($this->vat_rate) && $this->vat_rate !== '0.00';
    }

    /**
     * Checks whether current country is Lithuanian
     *
     * @return boolean
     */
    private function isLithuanianCountry()
    {
        return $this->code == 'LT';
    }
}
