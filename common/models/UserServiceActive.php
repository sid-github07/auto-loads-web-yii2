<?php

namespace common\models;

use backend\controllers\ClientController;
use common\components\MailLanguage;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "{{%user_service_active}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $service_id
 * @property integer $date_of_purchase
 * @property integer $status
 * @property integer $end_date
 * @property integer $credits
 * @property boolean $reminder
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Service $service
 * @property User $user
 */
class UserServiceActive extends ActiveRecord
{
    /** @const integer User service is not active */
    const NOT_ACTIVE = 0;
    
    /** @const integer User service is active */
    const ACTIVE = 1;
    
    /** @const integer User service is suspended */
    const SUSPENDED = 2;
    
    /** @const integer Minimum number of credits that user service can have */
    const MIN_CREDITS = 0;
    
    /** @const null Default subscription reminder value */
    const DEFAULT_REMINDER = null;
    
    /** @const integer Subscription reminder was sent successfully */
    const REMINDER_SEND = 1;
    
    /** @const integer Subscription reminder failed to send email */
    const REMINDER_FAILED = 0;
    
    /** @const string Subscription alert session key */
    const SHOW_SUBSCRIPTION_ALERT = 'show-subscription-alert';
    
    /** @const boolean Subscription alert session value */
    const SHOW_SUBSCRIPTION_ALERT_VALUE = false;
    
    /** @const integer How many days before subscription ending reminder should send remind email */
    const REMINDER_DAYS = 7;
    
    /** @const string Model scenario when creating new user service active */
    const SCENARIO_CREATE_SERVER = 'create';
    
    /** @const string Model scenario when administrator uses extended search filter to find clients  */
    const SCENARIO_EXTENDED_CLIENT_SEARCH = 'extended-client-search';
    
    /** @const string Model scenario when system sends subscription reminder to user email */
    const SCENARIO_SYSTEM_SENDS_SUBSCRIPTION_REMINDER = 'system-sends-subscription-reminder';
    
    /** @const string Model scenario when administrator changes user subscription end date */
    const SCENARIO_ADMIN_CHANGES_END_DATE = 'ADMIN_CHANGES_END_DATE';
    
    /** @const string Model scenario when administrator changes user subscription status */
    const SCENARIO_ADMIN_CHANGES_STATUS = 'ADMIN_CHANGES_STATUS';
    
    /** @const string Model scenario when administrator creates new company subscription */
    const SCENARIO_ADMIN_CREATES_NEW_SUBSCRIPTION = 'ADMIN_CREATES_NEW_SUBSCRIPTION';
    
    /** @const string Model scenario when system saves newly created subscription */
    const SCENARIO_SYSTEM_SAVES_NEW_SUBSCRIPTION = 'SYSTEM_SAVES_NEW_SUBSCRIPTION';
    
    /** @const string Model scenario when system migrates user service data from one database to another */
    const SCENARIO_SYSTEM_MIGRATES_USER_SERVICE_ACTIVE_DATA = 'system-migrates-user-service-active-data';
    
    const SCENARIO_SYSTEM_MIGRATES_USER_SERVICE_ACTIVE = 'system-migrates-user-service-active';
    
    /** @const string Model scenario to update end date when subscription time is used as credits */
    const SCENARIO_USE_SUBSCRIPTION_TIME = 'use-subscripion-time';
    
    /** @var date from for extended search */
    public $dateSubscribeFrom;
    
    /** @var date to for extended search */
    public $dateSubscribeTo;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_service_active}}';
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
            self::SCENARIO_CREATE_SERVER => [
                'user_id',
                'service_id',
                'date_of_purchase',
                'status',
                'end_date',
                'credits',
                'reminder',
                'created_at',
                'updated_at',
            ],
            self::SCENARIO_EXTENDED_CLIENT_SEARCH => [
                'status',
                'dateSubscribeTo',
                'dateSubscribeFrom'
            ],
            self::SCENARIO_SYSTEM_SENDS_SUBSCRIPTION_REMINDER => [
                'reminder',
            ],
            self::SCENARIO_ADMIN_CHANGES_END_DATE => [
                'end_date',
            ],
            self::SCENARIO_USE_SUBSCRIPTION_TIME => [
                'end_date',
            ],
            self::SCENARIO_ADMIN_CHANGES_STATUS => [
                'status'
            ],
            self::SCENARIO_ADMIN_CREATES_NEW_SUBSCRIPTION => [
                'user_id',
                'service_id',
                'date_of_purchase',
                'end_date',
            ],
            self::SCENARIO_SYSTEM_SAVES_NEW_SUBSCRIPTION => [
                'used_id',
                'service_id',
                'date_of_purchase',
                'status',
                'end_date',
                'credits',
                'reminder',
            ],
            self::SCENARIO_SYSTEM_MIGRATES_USER_SERVICE_ACTIVE_DATA => [
                'id',
                'user_id',
                'service_id',
                'date_of_purchase',
                'status',
                'end_date',
                'credits',
                'reminder',
                'created_at',
                'updated_at',
            ],
            self::SCENARIO_SYSTEM_MIGRATES_USER_SERVICE_ACTIVE => [
                'id',
                'user_id',
                'service_id',
                'date_of_purchase',
                'status',
                'end_date',
                'credits',
                'reminder',
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
            // User ID
            ['user_id', 'required', 'message' => Yii::t('app', 'USER_SERVICE_ACTIVE_USER_ID_IS_REQUIRED')],
            ['user_id', 'integer', 'message' => Yii::t('app', 'USER_SERVICE_ACTIVE_USER_ID_IS_NOT_INTEGER')],
            ['user_id', 'exist', 'targetClass' => User::className(),
                                 'targetAttribute' => ['user_id' => 'id'],
                                 'message' => Yii::t('app', 'USER_SERVICE_ACTIVE_USER_ID_NOT_EXIST')],
            
            // Service ID
            ['service_id', 'required', 'message' => Yii::t('app', 'USER_SERVICE_ACTIVE_SERVICE_ID_IS_REQUIRED')],
            ['service_id', 'integer', 'message' => Yii::t('app', 'USER_SERVICE_ACTIVE_SERVICE_ID_IS_NOT_INTEGER')],
            ['service_id', 'exist', 'targetClass' => Service::className(),
                                    'targetAttribute' => ['service_id' => 'id'],
                                    'message' => Yii::t('app', 'USER_SERVICE_ACTIVE_SERVICE_ID_NOT_EXIST')],
            
            // Date of purchase
            ['date_of_purchase', 'required', 'message' => Yii::t('app', 'USER_SERVICE_ACTIVE_DATE_OF_PURCHASE_IS_REQUIRED')],
            ['date_of_purchase', 'integer', 'message' => Yii::t('app', 'USER_SERVICE_ACTIVE_DATE_OF_PURCHASE_IS_NOT_INTEGER'),
                                            'on' => self::SCENARIO_SYSTEM_SAVES_NEW_SUBSCRIPTION,
                                            'except' => self::SCENARIO_ADMIN_CREATES_NEW_SUBSCRIPTION],
            ['date_of_purchase', 'date', 'format' => 'php:Y-m-d',
                                         'message' => Yii::t('app', 'USER_SERVICE_ACTIVE_DATE_OF_PURCHASE_INVALID_FORMAT', [
                                             'example' => date('Y-m-d'),
                                         ]),
                                         'on' => self::SCENARIO_ADMIN_CREATES_NEW_SUBSCRIPTION,
                                         'except' => self::SCENARIO_SYSTEM_SAVES_NEW_SUBSCRIPTION],
            
            // Status
            ['status', 'required', 'message' => Yii::t('app', 'USER_SERVICE_ACTIVE_STATUS_IS_REQUIRED'),
                                   'except' => self::SCENARIO_EXTENDED_CLIENT_SEARCH],
            ['status', 'integer', 'message' => Yii::t('app', 'USER_SERVICE_ACTIVE_STATUS_IS_NOT_INTEGER')],
            ['status', 'in', 'range' => [self::NOT_ACTIVE, self::ACTIVE, self::SUSPENDED],
                             'message' => Yii::t('app', 'USER_SERVICE_ACTIVE_STATUS_IS_NOT_IN_RANGE')],
            
            // End date
            ['end_date', 'required', 'message' => Yii::t('app', 'USER_SERVICE_ACTIVE_END_DATE_IS_REQUIRED')],
            ['end_date', 'integer', 'message' => Yii::t('app', 'USER_SERVICE_ACTIVE_END_DATE_IS_NOT_INTEGER'),
                                    'on' => self::SCENARIO_SYSTEM_SAVES_NEW_SUBSCRIPTION,
                                    'except' => self::SCENARIO_ADMIN_CREATES_NEW_SUBSCRIPTION],
            ['end_date', 'date', 'format' => 'php:Y-m-d',
                                 'message' => Yii::t('app', 'USER_SERVICE_ACTIVE_END_DATE_INVALID_FORMAT', [
                                     'example' => date('Y-m-d'),
                                 ]),
                                 'on' => self::SCENARIO_ADMIN_CREATES_NEW_SUBSCRIPTION,
                                 'except' => self::SCENARIO_SYSTEM_SAVES_NEW_SUBSCRIPTION],
            
            // Credits
            ['credits', 'required', 'message' => Yii::t('app', 'USER_SERVICE_ACTIVE_CREDITS_IS_REQUIRED')],
            ['credits', 'integer', 'min' => self::MIN_CREDITS,
                                   'tooSmall' => Yii::t('app', 'USER_SERVICE_ACTIVE_CREDITS_IS_TOO_SMALL', [
                                       'min' => self::MIN_CREDITS,
                                   ]),
                                   'message' => Yii::t('app', 'USER_SERVICE_ACTIVE_CREDITS_IS_NOT_INTEGER')],
            
            // Reminder
            ['reminder', 'default', 'value' => self::DEFAULT_REMINDER],
            ['reminder', 'integer', 'message' => Yii::t('app', 'USER_SERVICE_ACTIVE_IS_NOT_INTEGER')],
            ['reminder', 'in', 'range' => [self::REMINDER_FAILED, self::REMINDER_SEND],
                               'message' => Yii::t('app', 'USER_SERVICE_ACTIVE_REMINDER_NOT_IN_RANGE')],
            
            // Created at
            ['created_at', 'integer', 'message' => Yii::t('app', 'USER_SERVICE_ACTIVE_CREATED_AT_IS_NOT_INTEGER')],
            
            // Updated at
            ['updated_at', 'integer', 'message' => Yii::t('app', 'USER_SERVICE_ACTIVE_UPDATED_AT_IS_NOT_INTEGER')],
            
            // Date from
            ['dateSubscribeFrom', 'required', 'message' => Yii::t('app', 'COMPANY_DOCUMENT_DATE_IS_REQUIRED'),
                                              'except' => self::SCENARIO_EXTENDED_CLIENT_SEARCH],
            ['dateSubscribeFrom', 'integer', 'message' => Yii::t('app', 'COMPANY_DOCUMENT_DATE_IS_INTEGER'),
                                             'except' => self::SCENARIO_EXTENDED_CLIENT_SEARCH],
            
            //Date to
            ['dateSubscribeTo', 'required', 'message' => Yii::t('app', 'COMPANY_DOCUMENT_DATE_IS_REQUIRED'),
                                            'except' => self::SCENARIO_EXTENDED_CLIENT_SEARCH],
            ['dateSubscribeTo', 'integer', 'message' => Yii::t('app', 'COMPANY_DOCUMENT_DATE_IS_INTEGER'),
                                           'except' => self::SCENARIO_EXTENDED_CLIENT_SEARCH],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'USER_SERVICE_ACTIVE_USER_ID_LABEL'),
            'service_id' => Yii::t('app', 'USER_SERVICE_ACTIVE_TYPE_ID_LABEL'),
            'date_of_purchase' => Yii::t('app', 'USER_SERVICE_ACTIVE_DATE_OF_PURCHASE_LABEL'),
            'status' => Yii::t('app', 'USER_SERVICE_ACTIVE_STATUS_LABEL'),
            'end_date' => Yii::t('app', 'USER_SERVICE_ACTIVE_END_DATE_LABEL'),
            'credits' => Yii::t('app', 'USER_SERVICE_ACTIVE_CREDITS_LABEL'),
            'reminder' => Yii::t('app', 'USER_SERVICE_ACTIVE_REMINDER_LABEL'),
            'created_at' => Yii::t('app', 'USER_SERVICE_ACTIVE_CREATED_AT_LABEL'),
            'updated_at' => Yii::t('app', 'USER_SERVICE_ACTIVE_UPDATED_AT_LABEL'),
        ];
    }
    
    /**
     * @return ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Service::className(), ['id' => 'service_id']);
    }
    
    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    
    /**
     * Checks whether user active status is active
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->status == self::ACTIVE;
    }
    
    /**
     * Finds all user services that is active
     *
     * @param null|integer $userId User ID
     * @return static[]
     */
    public static function findAllActiveUserServices($userId = null)
    {
        if (is_null($userId)) {
            $userId = Yii::$app->user->id;
        }
        return self::find()->where(['user_id' => $userId, 'status' => self::ACTIVE])->all();
    }
    
    /**
     * Creates new active user service entry
     *
     * @param UserService $userService User service
     * @param Service $service Service
     * @param null|integer $userId User ID
     * @return boolean|self
     */
    public static function create(UserService $userService, Service $service, $userId = null)
    {
        $self = new self(['scenario' => self::SCENARIO_CREATE_SERVER]);
        $self->user_id = $userId;
        $self->service_id = $service->id;
        $self->date_of_purchase = time();
        $self->status = self::ACTIVE;
        $self->end_date = $userService->end_date;
        $self->credits = $service->credits;
        if ($self->save()) {
            return $self;
        }
        return false;
    }
    
    /**
     * Calculates when active user service should end
     *
     * @param integer $days Number of days to add
     * @param null|integer $date Start date
     * @return false|integer End date or false otherwise
     */
    public static function calculateEndDate($days = 0, $date = null)
    {
        if (is_null($date)) {
            $date = time();
        }
        return strtotime('+' . $days . ($days == 1 ? 'day' : 'days'), $date);
    }
    
    /**
     * Finds and returns last user active service date
     *
     * @param null|integer $userId User ID
     * @return array|null|self
     */
    public static function findLastEndDate($userId = null)
    {
        return self::find()
            ->where([
                'user_id' => $userId,
                'status' => self::ACTIVE
            ])
            ->orderBy(['end_date' => SORT_DESC])
            ->one();
    }
    
    /**
     * Returns active user service model from list of active user services by provided service type ID
     *
     * @param array $activeServices List of user active services
     * @param null|integer $serviceId Service type ID
     * @return boolean|self Active user service or false if model not found by service type ID
     */
    public static function getByServiceId($activeServices = [], $serviceId = null)
    {
        foreach ($activeServices as $key => $activeService) {
            if ($activeService->service_id == $serviceId) {
                return $activeService;
            }
        }
        return false;
    }
    
    /**
     * Extends active user service
     *
     * @param UserService $userService User service
     * @param Service $service Service
     * @return boolean|self
     */
    public function extend(UserService $userService, Service $service)
    {
        $this->date_of_purchase = $userService->start_date;
        $this->end_date = self::calculateEndDate($service->days, $this->end_date);
        $this->credits = $service->credits;
        if ($this->save()) {
            return $this;
        }
        return false;
    }
    
    /**
     * Checks whether subscription alert must be shown to user
     */
    public static function checkSubscriptionAlertVisibility()
    {
        $activeServices = self::findAllActiveUserServices();
        if (!empty($activeServices)) {
            self::hideSubscriptionAlert();
        }
    }
    
    /**
     * Hide subscription alert
     */
    public static function hideSubscriptionAlert()
    {
        Yii::$app->session->set(self::SHOW_SUBSCRIPTION_ALERT, self::SHOW_SUBSCRIPTION_ALERT_VALUE);
    }
    
    /**
     * Returns list of active user services with service name
     *
     * @param null|integer $userId User ID
     * @return array
     */
    public static function getAllActiveUserServicesWithName($userId = null)
    {
        if (is_null($userId)) {
            $userId = Yii::$app->user->id;
        }
        
        return self::findAllActiveUserServices($userId);
    }
    
    /**
     * Finds all active services that ending date is between current time and reminder days and did not get email
     *
     * @return array|ActiveRecord[]
     */
    public static function findAllEndingActiveServices()
    {
        $limit = self::calculateEndDate(self::REMINDER_DAYS, time());
        return self::find()
            ->where(['>=', 'end_date', time()])
            ->andWhere(['<=', 'end_date', $limit])
            ->andWhere(['status' => self::ACTIVE])
            ->andWhere(['OR',
                ['reminder' => self::DEFAULT_REMINDER],
                ['reminder' => self::REMINDER_FAILED],
            ])
            ->all();
    }
    
    /**
     * Sends subscription reminder to user active service owner email
     *
     * @return boolean Whether email was sent successfully
     */
    public function sendSubscriptionReminder()
    {
        $userLanguageIds = UserLanguage::getUserLanguages($this->id);
        
        MailLanguage::setMailLanguage($userLanguageIds);
        
        return Yii::$app->mailer->compose('subscription/reminder', [
            'companyName' => Yii::$app->params['companyName'],
        ])
            ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->params['companyName']])
            ->setTo($this->user->email)
            ->setSubject(Yii::t('mail', 'SUBSCRIPTION_REMINDER_SUBJECT', [
                'companyName' => Yii::$app->params['companyName'],
            ]))
            ->send();
    }
    
    /**
     * Finds all active users services
     *
     * @return static[]
     */
    public static function findAllActiveServices()
    {
        return self::findAll(['status' => self::ACTIVE]);
    }
    
    /**
     * Returns whether subscription alert must be shown
     *
     * @return boolean
     */
    public static function showSubscriptionAlert()
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        
        return !Yii::$app->session->has(self::SHOW_SUBSCRIPTION_ALERT);
    }
    
    /**
     * Returns list of translated active service options, extended search
     *
     * @return array
     */
    public function getTranslatedActiveService()
    {
        return [
            self::ACTIVE => Yii::t('app', 'COMPANY_WITH_ACTIVE_SUBSCRIPTION'),
            self::NOT_ACTIVE => Yii::t('app', 'COMPANY_WITHOUT_ACTIVE_SUBSCRIPTION'),
        ];
    }
    
    /**
     * Returns active data provider for company subscription
     *
     * @param null|integer $companyId Company ID
     * @param null|integer $year Selected subscription year
     * @return ActiveDataProvider
     */
    public static function getCompanySubscriptionsDataProvider($companyId, $year)
    {
        $beginningOfTheYear = strtotime($year . '-01-01');
        $endOfTheYear = strtotime($year . '-12-31');
        $query = self::find()
            ->joinWith('user')
            ->joinWith('user.companies AS ownerCompany')
            ->joinWith('user.companyUser')
            ->joinWith('user.companyUser.company AS userCompany')
            ->filterWhere([
                'or',
                ['ownerCompany.id' => $companyId],
                ['userCompany.id' => $companyId],
            ])
            ->andFilterWhere([
                'between',
                self::tableName() . '.created_at',
                $beginningOfTheYear,
                $endOfTheYear,
            ]);
        
        return new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'page' => (Yii::$app->request->get('page') - 1),
                'params' => ['tab' => ClientController::TAB_COMPANY_SUBSCRIPTIONS, 'id' => Yii::$app->request->get('id')],
            ]
        ]);
    }
    
    /**
     * Sets provided credits to current active user service
     *
     * @param null|integer $credits Number of credits that need to be set
     */
    public function setCredits($credits)
    {
        $this->credits = $credits;
    }
    
    /**
     * Converts current date_of_purchase date to timestamp when it is in string format
     */
    public function convertDateOfPurchaseToTimestamp()
    {
        if (is_string($this->date_of_purchase)) {
            $this->date_of_purchase = strtotime($this->date_of_purchase);
        }
    }
    
    /**
     * Converts current end_date date to timestamp when it is in string format
     */
    public function convertEndDateToTimestamp()
    {
        if (is_string($this->end_date)) {
            $this->end_date = strtotime($this->end_date);
        }
    }
    
    /**
     * Calculates how many days user is going to have TRIAL service
     *
     * @return float
     */
    public function calculateTrialDays()
    {
        return abs($this->date_of_purchase - $this->end_date) / 60 / 60 / 24; // Seconds converts to days
    }
    
    /**
     * Deletes active services for given user
     *
     * @param int $userId user id
     */
    public static function findAllActiveServicesToDelete($userId)
    {
        $rowsToDelete = self::find()
            ->joinWith('service')
            ->where(['user_id' => $userId])
            ->andWhere([ Service::tableName() . '.service_type_id' => 1])->all();
        
        return $rowsToDelete;
    }
    
    /**
     * Finds currently valid subscriptions and maps them with end date
     *
     * @return array
     */
    public static function findCurrentlyValidSubscriptions()
    {
        $activeSubscriptions = self::find()
            ->joinWith('service')
            ->where(['>', 'end_date', time()])
            ->andWhere([Service::tableName(). '.service_type_id' => ServiceType::MEMBER_TYPE_ID])
            ->all();
        
        return ArrayHelper::map($activeSubscriptions, 'user_id', 'end_date');
    }
    
    /**
     * Saves subscription as active and updates user credits
     *
     * @param ActiveRecord $subscription history item of user service
     */
    public function saveRightSubscriptions($subscription)
    {
        $this->scenario = self::SCENARIO_CREATE_SERVER;
        $this->user_id =  $subscription->user_id;
        $this->service_id =  $subscription->service_id;
        $this->date_of_purchase =  $subscription->start_date;
        $this->status = self::ACTIVE;
        $this->end_date =  $subscription->end_date;
        $this->credits =  $subscription->service->credits;
        $this->reminder =  null;
        $this->created_at =  time();
        $this->updated_at =  time();
        $this->user->updateCurrentCredits($subscription->service->credits);
        $this->save();
    }
    
    /**
     * Finds expired subscriptions
     *
     * @return array
     */
    public static function findExpiredSubscriptions()
    {
        return self::find()
            ->joinWith('service')
            ->where(['<=', 'end_date', time()])
            ->all();
    }
    
    public static function findActivatedSubscriptions($userId)
    {
        return self::find()
            ->joinWith('service')
            ->where([
                'user_id' => $userId,
                'status' => self::ACTIVE,
                Service::tableName() . '.service_type_id' => ServiceType::MEMBER_TYPE_ID,
            ])
            ->orderBy([
                'end_date' => SORT_DESC])
            ->one();
    }
    
    public static function findByIid($id)
    {
        return self::find()
            ->joinWith('service')
            ->where([self::tableName(). '.id' => $id])
            ->one();
    }
    
    /**
     * Deducts minutes from user service end date by specified credits
     * 
     * @param integer $credits
     * @return integer end time timestamp
     */
    public function deductEndDateMinutes($credits)
    {
        $minutes = $this->service->getMinutesByCredits($credits);
        $endDate = strtotime("-{$minutes} minutes", $this->end_date);
        
        if ($endDate < $this->date_of_purchase) {
            $endDate = $this->date_of_purchase;
        }
        $this->end_date = $endDate;
        return $endDate;
    }
    
    /**
     * Sets user service end date time by specified credits
     * Keeps existing scenario
     * 
     * @param integer $credits
     * @return integer end time timestamp
     */
    public function useTimeAsCredits($credits)
    {
        $endDate = $this->deductEndDateMinutes($credits);
        $currentScenario = $this->scenario;
        $this->setScenario(self::SCENARIO_USE_SUBSCRIPTION_TIME);
        $this->save();
        $this->setScenario($currentScenario);
        return $endDate;
    }
    
    /**
     * Returns user service hours
     * 
     * @return float hours
     */
    public function getHoursLeft()
    {
        $diff = $this->end_date - time();
        if ($diff < 0) {
            return 0;
        }
        return round($diff / (60 * 60), 3);
    }
    
    /**
     * Returns available to use user service credits from current expiry time
     * 
     * @return integer Credit amount
     */
    public function getAvailableCreditsFromEndTime()
    {
        $nominalHourPrice = $this->service->getNominalHourPrice();
        $hoursLeft = $this->getHoursLeft();
        return intval(round($hoursLeft / $nominalHourPrice));
    }
    
    /**
     * Returns related user service
     * 
     * @throws NotFoundHttpException If none or more than one related invoice was found
     * @return UserService
     */
    public function getRelatedUserService()
    {
        $services = UserService::find()
            ->where([
                'user_id' => $this->user_id,
                'service_id' => $this->service_id,
                'start_date' => $this->date_of_purchase,
            ])->all();
        
        if (count($services) !== 1) {
            throw new NotFoundHttpException(Yii::t('alert', 'RELATED_USER_SERVICE_FOR_USER_SERVICE_USER_NOT_FOUND'));
        }
        return $services[0];
    }
    
    /**
     * Removes if active services is expired
     */
    public function removeIfExpired()
    {
        if ($this->end_date < time()) {
            $this->delete();
        }
    }
}
