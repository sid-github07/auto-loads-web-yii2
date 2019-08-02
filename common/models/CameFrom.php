<?php

namespace common\models;

use common\components\Languages;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%came_from}}".
 *
 * @property integer $id
 * @property integer $language_id
 * @property string $source_name
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Language $language
 */
class CameFrom extends ActiveRecord
{
    /** @const integer Minimum length of source name */
    const SOURCE_NAME_MIN_LENGTH = 2;

    /** @const integer Maximum length of source name */
    const SOURCE_NAME_MAX_LENGTH = 255;
	
	/** @const integer to determine if need to load reason to register elements */
    const REASON_TO_REGISTER = 1;
    
    /** @const boolean Default type value */
    const TYPE_DEFAULT_VALUE = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%came_from}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Language ID
            ['language_id', 'required', 'message' => Yii::t('app', 'CAME_FROM_LANGUAGE_ID_IS_REQUIRED')],
            ['language_id', 'exist', 'targetClass' => Language::className(),
                                 'targetAttribute' => ['language_id' => 'id'],
                                         'message' => Yii::t('app', 'CAME_FROM_LANGUAGE_ID_IS_NOT_IN_RANGE')],

            // Source name
            ['source_name', 'required', 'message' => Yii::t('app', 'CAME_FROM_SOURCE_NAME_IS_REQUIRED')],
            ['source_name', 'string', 'min' => self::SOURCE_NAME_MIN_LENGTH,
                                 'tooShort' => Yii::t('app', 'CAME_FROM_SOURCE_NAME_IS_TOO_SHORT', [
                                     'length' => self::SOURCE_NAME_MIN_LENGTH
                                 ]),
                                      'max' => self::SOURCE_NAME_MAX_LENGTH,
                                  'tooLong' => Yii::t('app', 'CAME_FROM_SOURCE_NAME_IS_TOO_LONG', [
                                      'length' => self::SOURCE_NAME_MAX_LENGTH,
                                  ])],

            // Created at
            ['created_at', 'integer', 'message' => Yii::t('app', 'CAME_FROM_CREATED_AT_IS_NOT_INTEGER')],

            // Updated at
            ['updated_at', 'integer', 'message' => Yii::t('app', 'CAME_FROM_UPDATED_AT_IS_NOT_INTEGER')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'CAME_FROM_LABEL_ID'),
            'language_id' => Yii::t('app', 'CAME_FROM_LABEL_LANGUAGE_ID'),
            'source_name' => Yii::t('app', 'CAME_FROM_LABEL_SOURCE_NAME'),
            'created_at' => Yii::t('app', 'CAME_FROM_LABEL_CREATED_AT'),
            'updated_at' => Yii::t('app', 'CAME_FROM_LABEL_UPDATED_AT'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['id' => 'language_id']);
    }

    /**
     * Returns current website language ID
     *
     * @return false|null|string
     */
    private static function getCurrentLanguageId()
    {
        $countryCode = Languages::getCode();
        if ($countryCode == 'EN') {
            $countryCode = 'US';
        }
        return Language::getIdByCountryCode($countryCode);
    }

    /**
     * Returns source names
     *
     * @return array
     */
    public static function getSources()
    {
        $languageId = self::getCurrentLanguageId();
        return ArrayHelper::map(self::find()->where(['language_id' => $languageId, 'type' => self::REASON_TO_REGISTER])->all(), 'id', 'source_name');
    }
}
