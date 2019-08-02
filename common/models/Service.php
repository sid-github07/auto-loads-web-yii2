<?php

namespace common\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "{{%service}}".
 *
 * @property integer $id
 * @property integer $service_type_id
 * @property integer $days
 * @property string $price
 * @property string $name
 * @property integer $credits
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property ServiceType $serviceType
 * @property UserService[] $userServices
 */
class Service extends ActiveRecord
{
    /** @const integer Maximum number of characters that name can contain */
    const MAX_NAME_LENGTH = 255;

    /** @const integer Minimum number of credits */
    const MIN_CREDITS = 0;

    /** @const integer How many days have one month */
    const ONE_MONTH = 30;

    const TITLE_BASIC_CREDITS_20 = 'BASICCREDITS20';
    const TITLE_PREMIUM_CREDITS_45 = 'PREMIUMCREDITS45';
    const TITLE_GOLD_CREDITS_200 = 'GOLDCREDITSPLAN200';
    const TITLE_MEMBER_024 = 'MEMBER024';
    const TITLE_MEMBER_1 = 'MEMBER1';
    const TITLE_MEMBER_12 = 'MEMBER12';
    // this is load limit credits
    const TITLE_CREDITS_200 = 'CREDITS200';

    // this is credits that can buy actual services
    const TITLE_SERVICE_CREDITS = 'SERVICE_CREDITS';
    const TITLE_TRIAL = 'TRIAL';

    const TITLE_CREDITSCODE_20 = 'CREDITSCODE20';
    const TITLE_CREDITSCODE_100 = 'CREDITSCODE100';
    const TITLE_CREDITSCODE_1000 = 'CREDITSCODE1000';
    
    /**
     * @var array
     */
    public static $TITLES = [
        ServiceType::MEMBER_TYPE_ID => [
            self::TITLE_MEMBER_024,
            self::TITLE_MEMBER_1,
            self::TITLE_MEMBER_12,
        ],
        ServiceType::CREDITS_TYPE_ID => [
            self::TITLE_BASIC_CREDITS_20,
            self::TITLE_CREDITS_200,
        ],
        ServiceType::TRIAL_TYPE_ID => [
            self::TITLE_TRIAL,
        ],
        ServiceType::SERVICE_CREDITS_TYPE_ID => [
            self::TITLE_SERVICE_CREDITS,
        ],
        ServiceType::CREDITCODE_TYPE_ID => [
            self::TITLE_CREDITSCODE_20,
            self::TITLE_CREDITSCODE_100,
            self::TITLE_CREDITSCODE_1000,
        ],
    ];

    /**
     * @param $id
     * @return mixed|null
     */
    public static function getTitleByID($id)
    {
        if (isset(self::$TITLES[$id])) {
            $array = self::$TITLES[$id];
            if (count($array) > 1) {
                return $array;
            }

            return $array[0];
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%service}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Service type ID
            ['service_type_id', 'required', 'message' => Yii::t('app', 'SERVICE_SERVICE_TYPE_ID_IS_REQUIRED')],
            ['service_type_id', 'integer', 'message' => Yii::t('app', 'SERVICE_SERVICE_TYPE_ID_IS_NOT_INTEGER')],
            ['service_type_id', 'exist', 'targetClass' => ServiceType::className(),
                                     'targetAttribute' => ['service_type_id' => 'id'],
                                             'message' => Yii::t('app', 'SERVICE_SERVICE_TYPE_ID_NOT_EXIST')],

            // Days
            ['days', 'required', 'message' => Yii::t('app', 'SERVICE_DAYS_IS_REQUIRED')],
            ['days', 'integer', 'message' => Yii::t('app', 'SERVICE_DAYS_IS_NOT_INTEGER')],

            // Price
            ['price', 'required', 'message' => Yii::t('app', 'SERVICE_PRICE_IS_REQUIRED')],
            ['price', 'match', 'pattern' => '/^\d{1,8}(?:(\.|\,)\d{1,2})?$/',
                               'message' => Yii::t('app', 'SERVICE_PRICE_IS_NOT_MATCH')],

            // Name
            ['name', 'required', 'message' => Yii::t('app', 'SERVICE_NAME_IS_REQUIRED')],
            ['name', 'filter', 'filter' => 'trim'],
            ['name', 'string', 'max' => self::MAX_NAME_LENGTH,
                           'tooLong' => Yii::t('app', 'SERVICE_NAME_IS_TOO_LONG', [
                               'length' => self::MAX_NAME_LENGTH,
                           ])],

            // Credits
            ['credits', 'required', 'message' => Yii::t('app', 'SERVICE_CREDITS_IS_REQUIRED')],
            ['credits', 'integer', 'min' => self::MIN_CREDITS,
                              'tooSmall' => Yii::t('app', 'SERVICE_CREDITS_IS_TOO_SMALL', [
                                  'min' => self::MIN_CREDITS,
                              ])],
            ['label', 'required', 'message' => Yii::t('app', 'SERVICE_LABEL_IS_REQUIRED')],
            ['label', 'string', 'message' => Yii::t('app', 'SERVICE_LABEL_IS_NOT_STRING')],
            ['desc', 'string', 'message' => Yii::t('app', 'SERVICE_DESC_IS_NOT_STRING')],

            // Created at
            ['created_at', 'integer', 'message' => Yii::t('app', 'SERVICE_CREATED_AT_IS_NOT_INTEGER')],

            // Updated at
            ['updated_at', 'integer', 'message' => Yii::t('app', 'SERVICE_UPDATED_AT_IS_NOT_INTEGER')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'service_type_id' => Yii::t('app', 'SERVICE_SERVICE_TYPE_ID_LABEL'),
            'days' => Yii::t('app', 'SERVICE_DAYS_LABEL'),
            'price' => Yii::t('app', 'SERVICE_PRICE_LABEL'),
            'name' => Yii::t('app', 'SERVICE_NAME_LABEL'),
            'credits' => Yii::t('app', 'SERVICE_CREDITS_LABEL'),
            'label' => Yii::t('app', 'SERVICE_label_LABEL'),
            'desc' => Yii::t('app', 'SERVICE_desc_LABEL'),
            'created_at' => Yii::t('app', 'SERVICE_CREATED_AT_LABEL'),
            'updated_at' => Yii::t('app', 'SERVICE_UPDATED_AT_LABEL'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceType()
    {
        return $this->hasOne(ServiceType::className(), ['id' => 'service_type_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUserServices()
    {
        return $this->hasMany(UserService::className(), ['service_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public static function queryGetServices()
    {
        return self::find()
            ->orderBy('price');
    }

    /**
     * @return ActiveQuery
     */
    public static function queryGetUserAvailableServices()
    {
        return self::find()
            ->innerJoin(ServiceType::tableName(), ServiceType::tableName() . '.id = ' . self::tableName() . '.service_type_id')
            ->where(ServiceType::tableName() . '.order_by_user = ' . ServiceType::ALLOWED_FOR_USER)
            ->orderBy('price');
    }

    /**
     * @param int $creditType
     * @return array|ActiveRecord[]
     */
    public static function getUserAvailableCreditServices($creditType = ServiceType::SERVICE_CREDITS_TYPE_ID)
    {
        return self::queryGetUserAvailableServices()->where(ServiceType::tableName() . '.id = ' . $creditType)->all();

    }

    /**
     * @return array|ActiveRecord[]
     */
    public static function getUserAvailableSubscriptionServices()
    {
        return self::queryGetUserAvailableServices()->where(ServiceType::tableName() . '.id = ' . ServiceType::MEMBER_TYPE_ID)->all();
    }

    /**
     * Returns all services that user can order
     *
     * @return array|ActiveRecord[]
     */
    public static function getUserAvailableServices()
    {
        return self::queryGetUserAvailableServices()->all();
    }

    /**
     * Returns current service button ID by service name
     *
     * @return string
     */
    public function getButtonId()
    {
        if (isset(self::getButtonIds()[$this->name])) {
            return self::getButtonIds()[$this->name];
        }
        return null;
    }

    /**
     * Returns services buttons IDs
     *
     * @return array
     */
    public static function getButtonIds()
    {
        return [
            'MEMBER1' => 'PS-C-3',
            'MEMBER12' => 'PS-C-2',
            'MEMBER024' => 'PS-C-1c',
        ];
    }

    /**
     * Returns how many months is valid current service
     *
     * @return integer
     */
    public function getMonthsByDays()
    {
        return round($this->days / self::ONE_MONTH);
    }

    /**
     * Finds user available to order service by given service ID
     *
     * @param null|integer $id Service ID
     * @return array|null|ActiveRecord
     */
    public static function findUserAvailableById($id = null)
    {
        return self::find()
            ->innerJoin(ServiceType::tableName(), ServiceType::tableName() . '.id = ' . self::tableName() . '.service_type_id')
            ->where(ServiceType::tableName() . '.order_by_user = ' . ServiceType::ALLOWED_FOR_USER)
            ->andWhere([self::tableName() . '.id' => $id])
            ->one();
    }

    /**
     * Finds service by given user service ID
     *
     * @param null|integer $userServiceId User service ID
     * @return array|null|ActiveRecord
     */
    public static function findByUserServiceId($userServiceId = null)
    {
        return self::find()
            ->innerJoin(UserService::tableName(), UserService::tableName() . '.service_id = ' . self::tableName() . '.id')
            ->where([UserService::tableName() . '.id' => $userServiceId])
            ->one();
    }

    /**
     * Returns service name by provided user service ID
     *
     * @param null|integer $id User service ID
     * @return string
     */
    public static function getNameByUserServiceId($id = null)
    {
        /** @var self $service */
        $service = self::findByUserServiceId($id);
        return $service->name;
    }

    /**
     * Checks whether current service type is member
     *
     * @return boolean
     */
    public function isMemberType()
    {
        return $this->service_type_id == ServiceType::MEMBER_TYPE_ID;
    }

    /**
     * Checks whether current service type is member
     *
     * @return boolean
     */
    public function isCreditsServiceType()
    {
        return $this->service_type_id == ServiceType::SERVICE_CREDITS_TYPE_ID;
    }

    /**
     * Finds all admins and transfers them to active data provider
     *
     * @return ActiveDataProvider list of admins
     */
    public static function getDataProvider()
    {
        $query = self::queryGetServices();

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
     * Checks whether current service type is credits
     *
     * @return boolean
     */
    public function isCreditsType()
    {
        return $this->service_type_id == ServiceType::CREDITS_TYPE_ID;
    }

    /**
     * Checks whether service type is TRIAL
     *
     * @return boolean
     */
    public function isTrial()
    {
        return $this->service_type_id == ServiceType::TRIAL_TYPE_ID;
    }

    /**
     * Returns current service title
     *
     * @return string
     */
    public function getTitle($userServiceId = null)
    {
        // TODO: Implement multi-language names set in database
        $titles = self::getTitles();
        $placeholder = isset($titles[$this->name]) ? $titles[$this->name] : $this->name;
        $months = $this->getMonthsByDays();
        if ($months > 1) {
            return Yii::t('app', $placeholder, compact('months'));
        }
        if ($this->service_type_id == ServiceType::CREDITCODE_TYPE_ID) {
            $creditCode = "";
            $userService = UserService::findById($userServiceId);
            if ($userService->creditCode) {
                $creditCode = $userService->creditCode->creditcode;
            }
            return Yii::t('app', $placeholder . ' "{creditCode}"', compact('creditCode'));
        }
        return Yii::t('app', $placeholder);
    }

    /**
     * @return int
     */
    public function getCredits()
    {
        return $this->credits;
    }

    /**
     * Returns list of services titles
     * @deprecated
     * @return array
     */
    public static function getTitles()
    {
        return [
            self::TITLE_MEMBER_024 => 'SERVICE_ONE_DAY',
            self::TITLE_MEMBER_1 => 'SERVICE_ONE_MONTH',
            self::TITLE_MEMBER_12 => 'SERVICE_MANY_MONTHS',
            self::TITLE_BASIC_CREDITS_20 => 'BASIC_CREDITS_20',
            self::TITLE_CREDITS_200 => 'CREDITS_200',
            self::TITLE_TRIAL => 'TRIAL',
        ];
    }

    /**
     * Calculates the price for current service per one month
     *
     * @return float
     */
    public function calculatePricePerMonth()
    {
        $months = $this->getMonthsByDays();
        return round($this->price / $months, 1);
    }
    
    /**
     * Calculates nominal one credit euro price by BASICCREDITS20 service price
     *
     * @return float euro
     */
    public static function getNominalCreditPrice($title = self::TITLE_BASIC_CREDITS_20, $service = null)
    {
        if (is_null($service)) {
            $service = self::find()
                ->where(['name' => $title])->one();
        }
        if (is_null($service)) {
            throw new NotFoundHttpException(Yii::t('alert', 'BASIC_CREDITS_20_SERVICE_NOT_FOUND'));
        }
        
        return round($service->price / $service->credits, 2);
    }
    
    /**
     * Calculates service one hour euro price
     *
     * @return float euro
     */
    public function getHourPrice()
    {
        return Round($this->price / ($this->days * 24), 3);
    }
    
    /**
     * Calculates service one hour euro price
     *
     * @return float euro
     */
    public function getNominalHourPrice()
    {
        return Round(Service::getNominalCreditPrice() / $this->getHourPrice(), 1);
    }
    
    /**
     * Calculates subscription minute equivalent for given credits
     *
     * @param integer $credits
     * @return integer Minutes
     */
    public function getMinutesByCredits($credits)
    {
        $hours = round($this->getNominalHourPrice() * $credits, 1);
        return $hours * 60;
    }

    /**
     * Returns list of all translated account types
     *
     * @return array
     */
    public static function getCreditCodeServices()
    {
        return [
            self::TITLE_CREDITSCODE_20 => Yii::t('app', 'CREDITSCODE_NOMINAL', self::getPlaceholderforCreditCode(self::TITLE_CREDITSCODE_20)),
            self::TITLE_CREDITSCODE_100 => Yii::t('app', 'CREDITSCODE_NOMINAL', self::getPlaceholderforCreditCode(self::TITLE_CREDITSCODE_100)),
            self::TITLE_CREDITSCODE_1000 => Yii::t('app', 'CREDITSCODE_NOMINAL', self::getPlaceholderforCreditCode(self::TITLE_CREDITSCODE_1000)),
        ];
    }

    public static function getPlaceholderforCreditCode($title = self::TITLE_CREDITSCODE_20) {
        $service = self::find()
            ->where(['name' => $title])->one();
        return [
            'amount' => $service->credits,
            'price' => $service->price,
            'nominalPrice' => self::getNominalCreditPrice($title, $service),
        ];
    }
}
