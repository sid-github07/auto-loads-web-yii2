<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

/**
 * Class Announcement
 * @package common\models
 *
 * @property int $id
 * @property int $credit_cost
 */
class CreditService extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 0;

    static $STATUSES = [
        self::STATUS_ACTIVE => 'active',
        self::STATUS_DELETED => 'deleted',
    ];

    const CREDIT_TYPE_CAR_TRANSPORTER_DETAILS_VIEW = 1;
    const CREDIT_TYPE_CAR_TRANSPORTER_PREVIEW_VIEW = 2;
    const CREDIT_TYPE_LOAD_PREVIEW_VIEW = 3;
    const CREDIT_TYPE_OPEN_CONTACTS = 4;
    const CREDIT_TYPE_OPEN_POTENTIAL_HAULIERS = 5;
    const CREDIT_TYPE_OPEN_SEARCHES_IN_24H = 6;
    const CREDIT_TYPE_OPEN_TRUCKS = 7;
    const CREDIT_TYPE_OPEN_EXPIRED_OFFERS = 8;

    static $CREDIT_TYPES = [
        self::CREDIT_TYPE_CAR_TRANSPORTER_DETAILS_VIEW => 'car_transporter_details_view',
        self::CREDIT_TYPE_CAR_TRANSPORTER_PREVIEW_VIEW => 'car_transporter_preview_view',
        self::CREDIT_TYPE_LOAD_PREVIEW_VIEW => 'load_preview_view',
        self::CREDIT_TYPE_OPEN_CONTACTS => 'open_contacts',
        self::CREDIT_TYPE_OPEN_POTENTIAL_HAULIERS => 'open_potential_hauliers',
        self::CREDIT_TYPE_OPEN_SEARCHES_IN_24H => 'open_searches_in_24h',
        self::CREDIT_TYPE_OPEN_TRUCKS => 'open_trucks',
        self::CREDIT_TYPE_OPEN_EXPIRED_OFFERS => 'open_expired_offers',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%credit_services}}';
    }

    /**
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        if ($this->isNewRecord === true) {
            $this->created_at = date('Y-m-d H:i:s');
        } else {
            $this->updated_at = date('Y-m-d H:i:s');
        }
        return parent::save($runValidation, $attributeNames);
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['credit_type', 'credit_cost'], 'required'],
            [['credit_type'], 'integer'],
            [['credit_cost'], 'integer'],
            [['status'], 'integer'],
        ];
    }

    public function creditTypeString()
    {
        if ($this->isNewRecord === true || ! isset(self::$CREDIT_TYPES[$this->credit_type])) {
            return Yii::t('app', 'credit_type_unknown');
        }
        return self::$CREDIT_TYPES[$this->credit_type];
    }
    /**
     * @return mixed|string
     */
    public function statusString()
    {
        if (isset(self::$STATUSES[$this->status])) {
            return self::$STATUSES[$this->status];
        }

        return 'unknown';
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'credit_type' => Yii::t('app', 'Credit Type'),
            'credit_cost' => Yii::t('app', 'Credit Cost'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @param $model
     * @param int $creditType
     * @param bool $checkForSubscription
     * @return array|bool|null|ActiveRecord
     */
    public static function buy($model, $creditType = self::CREDIT_TYPE_CAR_TRANSPORTER_DETAILS_VIEW, $checkForSubscription = true)
    {
        $service = self::find()->where('credit_type =' . $creditType)->one();
        if (is_null($service)) {
            return false;
        }

        $user = Yii::$app->user;
        if ($user->isGuest === true) {
            return false;
        }

        $user = $user->identity;
        if  (($user->hasSubscription() === true && $checkForSubscription === true) || $user->hasBoughtService($model) === true) {
            return $service;
        }
        if ($user->hasEnoughServiceCredits($service->credit_cost) === true) {
            $userCredService = new UserCreditService();
            $attributes =  [
                'user_id' => $user->id,
                'credit_service_id' => $service->id,
                'entity_id' => $model->id,
                'entity_type' => $model->getEntityType(),
                'credit_service_type' => $creditType,
            ];
            $userCredService->attributes = $attributes;
            if ($userCredService->save() === true) {
                $user->useServiceCredits($service->credit_cost);
                $user->scenario = User::SCENARIO_UPDATE_SERVICE_CREDITS;
                $user->save();
            }
        }

        return $service;
    }

    /**
     * @return ActiveDataProvider
     */
    public static function getDataProvider()
    {
        $query = self::queryGetCreditServices();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => ['created_at'=> SORT_DESC]
            ],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $dataProvider;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['id' => 'language_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function queryGetCreditServices()
    {
        return self::find();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function queryGetActiveAnnouncements()
    {
        return self::queryGetCreditServices()->where( 'status =' . self::STATUS_ACTIVE);
    }

    /**
     * @param null $id
     * @return Announcement|null
     * @throws NotFoundHttpException
     */
    public static function findById($id = null)
    {
        $record = self::findOne($id);
        if (is_null($record)) {
            throw new NotFoundHttpException(Yii::t('alert', 'CREDIT_SERVICE_NOT_FOUND_BY_ID'));
        }

        return $record;
    }

    /**
     * Get CreditService credit cost by specified type
     * 
     * @param int $creditType
     * @return integer
     */
    public static function getCost($creditType)
    {
        $model = self::find()->where([
            'credit_type' => $creditType,
            'status' => self::STATUS_ACTIVE,
        ])->one();
        
        if (is_null($model)) {
            throw new NotFoundHttpException(Yii::t('alert', 'CREDIT_SERVICE_NOT_FOUND_BY_CREDIT_TYPE'));
        }
        
        return $model->credit_cost;
    }
}
