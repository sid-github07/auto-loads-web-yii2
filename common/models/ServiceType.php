<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%service_type}}".
 *
 * @property integer $id
 * @property string $name
 * @property boolean $order_by_user
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Service[] $services
 * @property UserServiceActive[] $userServiceActives
 */
class ServiceType extends ActiveRecord
{
    /** @const integer Maximum number of characters that name can have */
    const MAX_NAME_LENGTH = 255;

    /** @const boolean This type of service is allowed to order by user */
    const ALLOWED_FOR_USER = 1;

    /** @const boolean This type of service is forbidden to order by user */
    const FORBIDDEN_FOR_USER = 0;

    /** @const integer Member type ID in database */
    const MEMBER_TYPE_ID = 1;

    /** @const integer Credits type ID in database */
    const CREDITS_TYPE_ID = 2;

    /** @const integer Trial type ID in database */
    const TRIAL_TYPE_ID = 3;

    /** @const integer  Advertisement Credits type ID in database */
    const SERVICE_TYPE_SERVICE_CREDITS = 'SERVICE_CREDITS';
    const SERVICE_CREDITS_TYPE_ID = 4;

    /** @const integer Credit-code type ID in database */
    const CREDITCODE_TYPE_ID = 5;


    public static function getServiceTypes()
    {
        return [
            self::TRIAL_TYPE_ID => Yii::t('app','Trial'),
            self::MEMBER_TYPE_ID => Yii::t('app','Membership'),
            self::SERVICE_CREDITS_TYPE_ID => Yii::t('app','Service Credits'),
            self::CREDITS_TYPE_ID => Yii::t('app','Credits'),
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%service_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Name
            ['name', 'required', 'message' => Yii::t('app', 'SERVICE_TYPE_NAME_IS_REQUIRED')],
            ['name', 'filter', 'filter' => 'trim'],
            ['name', 'string', 'max' => self::MAX_NAME_LENGTH,
                           'tooLong' => Yii::t('app', 'SERVICE_TYPE_NAME_IS_TOO_LONG', [
                               'length' => self::MAX_NAME_LENGTH,
                           ])],

            // Order by user
            ['order_by_user', 'required', 'message' => Yii::t('app', 'SERVICE_TYPE_ORDER_BY_USER_IS_REQUIRED')],
            ['order_by_user', 'in', 'range' => [self::FORBIDDEN_FOR_USER, self::ALLOWED_FOR_USER],
                                  'message' => Yii::t('app', 'SERVICE_TYPE_ORDER_BY_USER_IS_NOT_IN_RANGE')],
            ['order_by_user', 'boolean', 'trueValue' => self::ALLOWED_FOR_USER,
                                        'falseValue' => self::FORBIDDEN_FOR_USER,
                                           'message' => Yii::t('app', 'SERVICE_TYPE_ORDER_BY_USER_IS_NOT_BOOLEAN')],

            // Created at
            ['created_at', 'integer', 'message' => Yii::t('app', 'SERVICE_TYPE_CREATED_AT_IS_NOT_INTEGER')],

            // Updated at
            ['updated_at', 'integer', 'message' => Yii::t('app', 'SERVICE_TYPE_UPDATED_AT_IS_NOT_INTEGER')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'SERVICE_TYPE_NAME_LABEL'),
            'order_by_user' => Yii::t('app', 'SERVICE_TYPE_ORDER_BY_USER_LABEL'),
            'created_at' => Yii::t('app', 'SERVICE_TYPE_CREATED_AT_LABEL'),
            'updated_at' => Yii::t('app', 'SERVICE_TYPE_UPDATED_AT_LABEL'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getServices()
    {
        return $this->hasMany(Service::className(), ['service_type_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUserServiceActives()
    {
        return $this->hasMany(UserServiceActive::className(), ['service_type_id' => 'id']);
    }

    /**
     * Returns translated server type names
     *
     * @return array
     */
    public static function getTranslatedNames()
    {
        return ArrayHelper::map(self::find()->all(), 'id', function (self $serviceType) {
            return Yii::t('app', $serviceType->name);
        });
    }
}
