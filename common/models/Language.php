<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%language}}".
 *
 * @property integer $id
 * @property string $country_code
 * @property string $name
 *
 * @property UserLanguage[] $userLanguages
 */
class Language extends ActiveRecord
{
    /** @const integer Minimum length of country code */
    const COUNTRY_CODE_MIN_LENGTH = 2;

    /** @const integer Maximum length of country code */
    const COUNTRY_CODE_MAX_LENGTH = 2;

    /** @const integer Maximum length of language name */
    const NAME_MAX_LENGTH = 255;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%language}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Country code
            ['country_code', 'required'],
            ['country_code', 'string', 'min' => self::COUNTRY_CODE_MIN_LENGTH,
                                       'max' => self::COUNTRY_CODE_MAX_LENGTH],

            // Name
            ['name', 'required'],
            ['name', 'string', 'max' => self::NAME_MAX_LENGTH],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'country_code' => Yii::t('app', 'Country Code'),
            'name' => Yii::t('app', 'Name'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUserLanguages()
    {
        return $this->hasMany(UserLanguage::className(), ['language_id' => 'id']);
    }

    /**
     * Returns names of all languages
     *
     * @return array
     */
    public static function getNames()
    {
        return ArrayHelper::map(self::find()->all(), 'id', 'name');
    }

    /**
     * Returns language ID by given country code
     *
     * @param string $code Language name short version
     * @return false|null|string
     */
    public static function getIdByCountryCode($code = '')
    {
        return self::find()->select('id')->where(['country_code' => $code])->scalar();
    }

    /**
     * Returns names of all languages with that language flag icon
     *
     * @return array|null
     */
    public static function getIconicNames()
    {
        /** @var self[] $languages */
        $languages = self::find()->all();
        if (is_null($languages)) {
            return null;
        }

        $names = [];
        foreach ($languages as $language) {
            $names[$language->id] = Html::tag('i', null, [
                'class' => 'flag-icon flag-icon-' . strtolower($language->country_code)
            ]) . ' ' . $language->name;
        }

        return $names;
    }
    
    /**
     * Returns users languages formatted with flag icons
     * 
     * @param number $userId Users ID
     * @return array
     */
    public static function getUserSelectedLanguages($userId = null)
    {
        $userLanguages = self::find()->joinWith('userLanguages')->where(['user_id' => $userId])->all();
        
        $iconicLanguage = [];
        foreach ($userLanguages as $i => $language) {
            $iconicLanguage[$i] = '<span>' . Html::tag('i', null, [
                'class' => 'flag-icon flag-icon-' . strtolower($language->country_code)
            ]) . ' ' . $language->name . '</span>' . '<span class="separator">, </span>';
        }
        
        return $iconicLanguage;
    }

    /**
     * Finds and returns user languages
     *
     * @param integer $userId User ID
     * @return array|self[]
     */
    public static function findUserLanguages($userId)
    {
        $languages = self::find()
            ->joinWith('userLanguages')
            ->where(['user_id' => $userId])
            ->all();

        return $languages;
    }
}
