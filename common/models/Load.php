<?php

namespace common\models;

use backend\controllers\ClientController;
use common\components\audit\Log;
use common\components\audit\SystemMessage;
use DateTime;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\db\Query;

/**
 * This is the model class for table "{{%load}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $code
 * @property integer $type
 * @property integer $payment_method
 * @property integer $date
 * @property string $price
 * @property integer $status
 * @property integer $active
 * @property integer $transported
 * @property integer $date_of_expiry
 * @property string $token
 * @property string $phone
 * @property string $email
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $show_contacts
 *
 * @property User $user
 * @property LoadCar[] $loadCars
 * @property LoadCity[] $loadCities
 * @property LoadPreview[] $loadPreviews
 */
class Load extends ActiveRecord
{
    /** @const integer Number of credits that user needs to spend in order to reactivate expired load  */
    const EXPIRED_LOAD_REACTIVATION_CREDITS = 1;

    /** @const integer Number of credits that user needs to spend in order to announce new load */
    const LOAD_ANNOUNCEMENT_CREDITS = 1;

    /** @const integer Number of credits that user needs to spend in order to preview load information */
    const LOAD_PREVIEW_CREDITS = 1;

    /** @const null Default user ID value */
    const DEFAULT_USER_ID = null;

    /** @const integer Maximum number of characters that load code can contain */
    const CODE_MAX_LENGTH = 255;

    /** @const null Default code value */
    const DEFAULT_CODE = null;

    /** @const integer Load type is partial */
    const TYPE_PARTIAL = 0;

    /** @const integer Load type is full */
    const TYPE_FULL = 1;

    /** @const integer Minimum number of cars that load can have when load type is full */
    const TYPE_LIMIT = 8;

    /** @const integer Payment method is for car model */
    const FOR_CAR_MODEL = 0;

    /** @const integer Payment method is for all load */
    const FOR_ALL_LOAD = 1;

    /** @const integer Number of characters that price can contain before comma or dot */
    const PRICE_PRECISION = 10;

    /** @const integer Number of characters that price can contain after comma or dot */
    const PRICE_SCALE = 2;

    /** @const null Default price value */
    const DEFAULT_PRICE = null;

    /** @const integer Load is inactive */
    const INACTIVE = 0;

    /** @const integer Load is active */
    const ACTIVE = 1;

    /** @const integer Load transported default value */
    const DEFAULT_TRANSPORTED = 0;

    /** @const integer Load transported value */
    const TRANSPORTED_YES = 1;
    
    /** @const integer Load transported value */
    const TRANSPORTED_NO = 0;
    
    /** @const integer Load transported value - expired load without previews */
    const TRANSPORTED_NO_PREVIEWS = 2;

    /** @const null Default active value */
    const DEFAULT_ACTIVE = null;

    /** @const integer Load is not activated */
    const NOT_ACTIVATED = 0;

	/** @const integer Load then filtering both active and inactive loads */
    const BOTH = 2;

    /** @const integer Load is activated */
    const ACTIVATED = 1;

    /** @const integer How many work days need to pass in order to make load as inactive */
    const DAYS_TO_EXPIRE = 7;

    /** @const null Default token value */
    const DEFAULT_TOKEN = null;

    /** @const integer Maximum number of characters that load token can contain */
    const TOKEN_MAX_LENGTH = 255;

    /** @const integer Number of characters that token has */
    const TOKEN_LENGTH = 64;

    /** @const null Default load owner phone value */
    const DEFAULT_PHONE = null;

    /** @const integer Maximum number of characters that load owner phone number can contain */
    const PHONE_MAX_LENGTH = 255;

    /** @const null Default load owner email value */
    const DEFAULT_EMAIL = null;

    /** @const integer Maximum number of characters that load owner email can contain */
    const EMAIL_MAX_LENGTH = 255;

    /** @const string Specific symbol that is used in load code */
    const LOAD_CODE_SYMBOL = 'K';

    /** @const integer First page size option */
    const FIRST_PAGE_SIZE = 50;

    /** @const integer Second page size option */
    const SECOND_PAGE_SIZE = 100;

    /** @const integer Third page size option */
    const THIRD_PAGE_SIZE = 150;

    /** @const integer First search radius option */
    const FIRST_RADIUS = 50;

    /** @const integer Second search radius option */
    const SECOND_RADIUS = 100;

    /** @const integer Third search radius option */
    const THIRD_RADIUS = 150;

	/** @const boolean Value when search returned results */
    const SEARCH_HAS_RESULTS = 1;

    /** @const boolean Value when search returned no results */
    const SEARCH_NO_RESULTS = 0;

    /** @const null Default suggestions type value */
    const DEFAULT_SUGGESTIONS_TYPE = null;

    /** @const integer Suggestion type is direct transportation */
    const DIRECT_SUGGESTIONS = 1;

    /** @const integer Suggestions type is additional transportation */
    const ADDITIONAL_SUGGESTIONS = 2;

    /** @const integer Suggestions type is full unload transportation */
    const FULL_UNLOAD_SUGGESTIONS = 3;

    /** @const string Value when load date is not set */
    const DATE_NOT_SET = '—';

    /** @const string Model scenario when user announces new load */
    const SCENARIO_ANNOUNCE_CLIENT = 'announce-client';

    /** @const string Model scenario when user announced new load must be saved to database */
    const SCENARIO_ANNOUNCE_SERVER = 'announce-server';

    /** @const string Model scenario when load status must be changed */
    const SCENARIO_CHANGE_STATUS = 'change-status';

    /** @const string Model scenario when load deleted */
    const SCENARIO_CHANGE_ACTIVE = 'change-active';

    /** @const string Model scenario when load deleted */
    const SCENARIO_EDIT_LOAD_INFO = 'edit-load-info';

    /** @const string Model scenario when load date is changed */
    const SCENARIO_EDIT_LOAD_DATE = 'edit-load-date';

    /** @const string Model scenario when load code must be updated */
    const SCENARIO_UPDATE_CODE = 'update-code';

    /** @const string Model scenario when client searches loads */
    const SCENARIO_SEARCH_CLIENT = 'search-client';

    /** @const string Model scenario when client filters loads suggestions */
    const SCENARIO_CLIENT_FILTERS_LOADS_SUGGESTIONS = 'client-filters-loads-suggestions';

    /** @const string Model scenario when administrator filters loads */
    const SCENARIO_ADMIN_FILTERS_LOADS = 'admin-filters-loads';

    /** @const string Model scenario when user reactivates loads */
    const SCENARIO_USER_REACTIVATES_LOADS = 'user-reactivates-loads';

    /** @const string Model scenario when system migrates load data from one database to another */
    const SCENARIO_SYSTEM_MIGRATES_LOAD_DATA = 'system-migrates-load-data';

    const SCENARIO_SYSTEM_MIGRATES_LOAD = 'system-migrates-load';

    const SCENARIO_UPDATE_LOAD_ADV = 'update-load-advertisement';

    /** @const string SCENARIO_UPDATE_OPEN_CONTACTS scenario to update open contact attributes */
    const SCENARIO_UPDATE_OPEN_CONTACTS = 'open-contacts';

    /** @const string Hidden suggestions session key */
    const HIDDEN_SUGGESTIONS = 'hidden-suggestions';

    /** @const integer used to identify which loads should be shown in search for full and additional loads */
    const MATCH_NUMBER = 2;

    const ENTITY_TYPE_LOAD = 'load';

    /** @var integer Load search radius attribute */
    public $searchRadius;

    /** @var boolean Attribute, whether load date is being filtered */
    public $filterDate = false;

    /** @var integer Loads suggestions type attribute */
    public $suggestionsType;

    /** @var string Loads filtration date range beginning */
    public $dateFrom;

    /** @var string Loads filtration date range ending */
    public $dateTo;

	/** @var boolean Loads search parameter to set if search found any matching loads */
    public $haveResults = self::SEARCH_HAS_RESULTS;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%load}}';
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
            self::SCENARIO_ANNOUNCE_CLIENT => [
                'type',
                'payment_method',
                'date',
                'price',
                'phone',
                'email',
                'days_adv',
                'car_pos_adv',
                'submit_time_adv',
                'open_contacts_days',
                'open_contacts_expiry',
            ],
            self::SCENARIO_ANNOUNCE_SERVER => [
                'user_id',
                'code',
                'type',
                'payment_method',
                'date',
                'price',
                'status',
                'active',
                'date_of_expiry',
                'token',
                'phone',
                'email',
                'days_adv',
                'car_pos_adv',
                'submit_time_adv',
                'open_contacts_days',
                'open_contacts_expiry',
            ],
            self::SCENARIO_CHANGE_STATUS => [
                'status',
            ],
            self::SCENARIO_CHANGE_ACTIVE => [
                'active',
                'date_of_expiry',
                'transported',
            ],
            self::SCENARIO_EDIT_LOAD_INFO => [
                'payment_method',
                'price',
            ],
            self::SCENARIO_EDIT_LOAD_DATE => [
                'date',
            ],
            self::SCENARIO_UPDATE_LOAD_ADV => [
                'days_adv',
                'car_pos_adv',
                'submit_time_adv',
            ],
            self::SCENARIO_UPDATE_OPEN_CONTACTS => [
                'open_contacts_days',
                'open_contacts_expiry',
            ],
            self::SCENARIO_UPDATE_CODE => [
                'code',
            ],
            self::SCENARIO_SEARCH_CLIENT => [
                'searchRadius',
                'date',
            ],
            self::SCENARIO_CLIENT_FILTERS_LOADS_SUGGESTIONS => [
                'date',
                'suggestionsType',
            ],
            self::SCENARIO_ADMIN_FILTERS_LOADS => [
                'dateFrom',
                'dateTo',
                'type',
            ],
            self::SCENARIO_USER_REACTIVATES_LOADS => [
                'date_of_expiry',
            ],
            self::SCENARIO_SYSTEM_MIGRATES_LOAD_DATA => [
                'id',
                'user_id',
                'code',
                'type',
                'payment_method',
                'date',
                'price',
                'status',
                'active',
                'date_of_expiry',
                'token',
                'phone',
                'email',
                'created_at',
                'updated_at',
                'days_adv',
                'car_pos_adv'
            ],
            self::SCENARIO_SYSTEM_MIGRATES_LOAD => [
                'id',
                'user_id',
                'code',
                'type',
                'payment_method',
                'date',
                'price',
                'status',
                'active',
                'date_of_expiry',
                'token',
                'phone',
                'email',
                'created_at',
                'updated_at',
                'days_adv',
                'car_pos_adv',
                'submit_time_adv',
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
            ['user_id', 'required', 'when' => function () {
                                        return !Yii::$app->user->isGuest;
                                    },
                                 'message' => Yii::t('app', 'LOAD_USER_ID_IS_REQUIRED')],
            ['user_id', 'default', 'value' => self::DEFAULT_USER_ID],
            ['user_id', 'integer', 'message' => Yii::t('app', 'LOAD_USER_ID_IS_NOT_INTEGER'),
                                    'except' => self::SCENARIO_ANNOUNCE_SERVER],
            ['user_id', 'exist', 'targetClass' => User::className(),
                             'targetAttribute' => ['user_id' => 'id'],
                                     'message' => Yii::t('app', 'LOAD_USER_ID_IS_NOT_EXIST')],

            // Code
            ['code', 'required', 'message' => Yii::t('app', 'LOAD_CODE_IS_REQUIRED'),
                                  'except' => [
                                      self::SCENARIO_ANNOUNCE_SERVER,
                                      self::SCENARIO_SYSTEM_MIGRATES_LOAD_DATA,
                                      self::SCENARIO_SYSTEM_MIGRATES_LOAD,
                                  ]],
            ['code', 'default', 'value' => self::DEFAULT_CODE],
            // NOTE: if 'skipOnEmpty' is false, then code becomes empty string instead of null
            ['code', 'filter', 'filter' => 'trim', 'skipOnEmpty' => true],
            ['code', 'unique', 'targetClass' => self::className(),
                                   'message' => Yii::t('app', 'LOAD_CODE_IS_NOT_UNIQUE')],
            ['code', 'string', 'max' => self::CODE_MAX_LENGTH,
                           'tooLong' => Yii::t('app', 'LOAD_CODE_IS_TOO_LONG', [
                               'length' => self::CODE_MAX_LENGTH,
                           ]),
                           'message' => Yii::t('app', 'LOAD_CODE_IS_NOT_STRING')],

            // Type
            ['type', 'required', 'message' => Yii::t('app', 'LOAD_TYPE_IS_REQUIRED'),
                                  'except' => self::SCENARIO_ADMIN_FILTERS_LOADS],
            ['type', 'integer', 'message' => Yii::t('app', 'LOAD_TYPE_IS_NOT_INTEGER')],
            ['type', 'in', 'range' => [self::TYPE_PARTIAL, self::TYPE_FULL],
                         'message' => Yii::t('app', 'LOAD_TYPE_IS_NOT_IN_RANGE')],

            // Payment method
            ['payment_method', 'required', 'message' => Yii::t('app', 'LOAD_PAYMENT_METHOD_IS_REQUIRED')],
            ['payment_method', 'integer', 'message' => Yii::t('app', 'LOAD_PAYMENT_METHOD_IS_NOT_INTEGER')],
            ['payment_method', 'in', 'range' => [self::FOR_CAR_MODEL, self::FOR_ALL_LOAD],
                                   'message' => Yii::t('app', 'LOAD_PAYMENT_METHOD_IS_NOT_IN_RANGE')],

            // Date
            ['date', 'integer', 'message' => Yii::t('app', 'LOAD_DATE_IS_NOT_INTEGER'),
                                     'on' => [self::SCENARIO_ANNOUNCE_SERVER, self::SCENARIO_EDIT_LOAD_DATE],
                                 'except' => self::SCENARIO_CLIENT_FILTERS_LOADS_SUGGESTIONS],
            ['date', 'date', 'format' => 'php:Y-m-d',
                            'message' => Yii::t('app', 'LOAD_DATE_IS_NOT_MATCH', [
                                'example' => date('Y-m-d'),
                            ]),
                                 'on' => [self::SCENARIO_CLIENT_FILTERS_LOADS_SUGGESTIONS, self::SCENARIO_ANNOUNCE_CLIENT],
                             'except' => [self::SCENARIO_ANNOUNCE_SERVER, self::SCENARIO_EDIT_LOAD_DATE]],
            ['date', 'validateDate', 'params' => ['message' => Yii::t('app', 'LOAD_DATE_CANNOT_BE_LESS_THAN_TODAY')],
                                     'except' => [
                                         self::SCENARIO_ANNOUNCE_SERVER,
                                         self::SCENARIO_EDIT_LOAD_DATE,
                                         self::SCENARIO_SYSTEM_MIGRATES_LOAD_DATA,
                                         self::SCENARIO_SYSTEM_MIGRATES_LOAD,
                                     ]],

            // Price
            ['price', 'default', 'value' => self::DEFAULT_PRICE],
            ['price', 'match', 'pattern' => '/^\d{1,8}(?:(\.|\,)\d{1,2})?$/',
                               'message' => Yii::t('app', 'LOAD_PRICE_IS_NOT_MATCH')],

            // Status
            ['status', 'required', 'message' => Yii::t('app', 'LOAD_STATUS_IS_REQUIRED')],
            ['status', 'integer', 'message' => Yii::t('app', 'LOAD_STATUS_IS_NOT_INTEGER')],
            ['status', 'in', 'range' => [self::INACTIVE, self::ACTIVE],
                           'message' => Yii::t('app', 'LOAD_STATUS_IS_NOT_IN_RANGE')],

            // Active
            ['active', 'default', 'value' => self::DEFAULT_ACTIVE],
            ['active', 'integer', 'message' => Yii::t('app', 'LOAD_ACTIVE_IS_NOT_INTEGER')],
            ['active', 'in', 'range' => self::getActivities(),
                           'message' => Yii::t('app', 'LOAD_ACTIVE_IS_NOT_IN_RANGE')],
                                            
            // Transported
            ['active', 'default', 'value' => self::DEFAULT_TRANSPORTED],
            ['active', 'integer', 'message' => Yii::t('app', 'LOAD_TRANSPORTED_IS_NOT_INTEGER')],
            ['active', 'in', 'range' => [
                    self::TRANSPORTED_NO,
                    self::TRANSPORTED_YES,
                    self::TRANSPORTED_NO_PREVIEWS,
                ],
                'message' => Yii::t('app', 'LOAD_TRANSPORTED_IS_NOT_IN_RANGE')
            ],

            // Date of expiry
            ['date_of_expiry', 'required', 'message' => Yii::t('app', 'LOAD_DATE_OF_EXPIRY_IS_REQUIRED')],
            ['date_of_expiry', 'integer', 'message' => Yii::t('app', 'LOAD_DATE_OF_EXPIRY_IS_NOT_INTEGER')],

            // Token
            ['token', 'default', 'value' => self::DEFAULT_TOKEN],
            // NOTE: if 'skipOnEmpty' is false, then token becomes empty string instead of null
            ['token', 'filter', 'filter' => 'trim', 'skipOnEmpty' => true],
            ['token', 'unique', 'targetClass' => self::className(),
                                    'message' => Yii::t('app', 'LOAD_TOKEN_IS_NOT_UNIQUE')],
            ['token', 'string', 'max' => self::TOKEN_MAX_LENGTH,
                            'tooLong' => Yii::t('app', 'LOAD_TOKEN_IS_TOO_LONG', [
                                'length' => self::TOKEN_MAX_LENGTH,
                            ]),
                            'message' => Yii::t('app', 'LOAD_TOKEN_IS_NOT_STRING')],

            // Phone
            ['phone', 'required', 'when' => function () {
                    return Yii::$app->user->isGuest;
                },
                'message' => Yii::t('app', 'LOAD_PHONE_IS_REQUIRED'),
                'except' => [
                    self::SCENARIO_SYSTEM_MIGRATES_LOAD_DATA,
                    self::SCENARIO_SYSTEM_MIGRATES_LOAD,
                ]
            ],
            ['phone', 'default', 'value' => self::DEFAULT_PHONE],
            // NOTE: if 'skipOnEmpty' is false, then phone becomes empty string instead of null
            ['phone', 'filter', 'filter' => 'trim', 'skipOnEmpty' => true],
            ['phone', 'match', 'pattern' => '/^[+][0-9]{1,4}[0-9]{3,12}$/',
                               'message' => Yii::t('app', 'LOAD_PHONE_IS_NOT_MATCH'),
                                'except' => self::SCENARIO_SYSTEM_MIGRATES_LOAD],
            ['phone', 'string', 'max' => self::PHONE_MAX_LENGTH,
                            'tooLong' => Yii::t('app', 'LOAD_PHONE_IS_TOO_LONG', [
                                'length' => self::PHONE_MAX_LENGTH,
                            ]),
                            'message' => Yii::t('app', 'LOAD_PHONE_IS_NOT_STRING'),
                             'except' => self::SCENARIO_SYSTEM_MIGRATES_LOAD],

            // Email
            ['email', 'required', 'when' => function () {
                                      return Yii::$app->user->isGuest;
                                  },
                               'message' => Yii::t('app', 'LOAD_EMAIL_IS_REQUIRED'),
                                'except' => [
                                    self::SCENARIO_SYSTEM_MIGRATES_LOAD_DATA,
                                    self::SCENARIO_SYSTEM_MIGRATES_LOAD,
                                ]],
            ['email', 'default', 'value' => self::DEFAULT_EMAIL],
            // NOTE: if 'skipOnEmpty' is false, then email becomes empty string instead of null
            ['email', 'filter', 'filter' => 'trim', 'skipOnEmpty' => true],
            ['email', 'email', 'message' => Yii::t('app', 'LOAD_EMAIL_IS_NOT_EMAIL'),
                                'except' => self::SCENARIO_SYSTEM_MIGRATES_LOAD],
            ['email', 'string', 'max' => self::EMAIL_MAX_LENGTH,
                'tooLong' => Yii::t('app', 'LOAD_EMAIL_IS_TOO_LONG', [
                    'length' => self::EMAIL_MAX_LENGTH,
                ]),
                'message' => Yii::t('app', 'LOAD_EMAIL_IS_NOT_STRING'),
                 'except' => self::SCENARIO_SYSTEM_MIGRATES_LOAD],
            ['email', 'validateEmail', 'when' => function () {
                    return Yii::$app->user->isGuest;
                },
                'params' => ['message' => Yii::t('app', 'LOAD_EMAIL_ALREADY_EXISTS')],
                'except' => self::SCENARIO_SYSTEM_MIGRATES_LOAD],

            // Created at
            ['created_at', 'integer', 'message' => Yii::t('app', 'LOAD_CREATED_AT_IS_NOT_INTEGER')],

            // Updated at
            ['updated_at', 'integer', 'message' => Yii::t('app', 'LOAD_UPDATED_AT_IS_NOT_INTEGER')],

            // Search radius
            ['searchRadius', 'required', 'message' => Yii::t('app', 'LOAD_SEARCH_RADIUS_IS_REQUIRED')],
            ['searchRadius', 'integer', 'message' => Yii::t('app', 'LOAD_SEARCH_RADIUS_IS_NOT_INTEGER')],
            ['searchRadius', 'in', 'range' => array_keys(self::getSearchRadius()),
                                 'message' => Yii::t('app', 'LOAD_SEARCH_RADIUS_IS_NOT_IN_RANGE')],

            // Suggestions type
            ['suggestionsType', 'in', 'range' => array_keys(self::getSuggestionsTypes()),
                                         'on' => self::SCENARIO_CLIENT_FILTERS_LOADS_SUGGESTIONS],

            // Date from
            ['dateFrom', 'string', 'message' => Yii::t('app', 'LOAD_DATE_FROM_NOT_STRING')],
            ['dateFrom', 'date', 'format' => 'php:Y-m-d',
                                'message' => Yii::t('app', 'LOAD_DATE_FROM_INVALID_FORMAT', [
                                    'example' => date('Y-m-d'),
                                ])],
            ['dateFrom', 'validateDateRange', 'params' => [
                'emptyMessage' => Yii::t('app', 'LOAD_DATE_FROM_EMPTY_DATES'),
                'invalidMessage' => Yii::t('app', 'LOAD_DATE_FROM_IS_INVALID'),
            ]],

            // Date to
            ['dateTo', 'string', 'message' => Yii::t('app', 'LOAD_DATE_TO_NOT_STRING')],
            ['dateTo', 'date', 'format' => 'php:Y-m-d',
                              'message' => Yii::t('app', 'LOAD_DATE_TO_INVALID_FORMAT', [
                                  'example' => date('Y-m-d'),
                              ])],

            // days_adv
            ['days_adv', 'integer', 'message' => Yii::t('app', 'CAR_ADV_DAY_IS_NOT_INTEGER')],

            // car_pos_adv
            ['car_pos_adv', 'integer', 'message' => Yii::t('app', 'CAR_POS_ADV_IS_NOT_INTEGER')],
            ['submit_time_adv', 'date', 'format' => 'php:Y-m-d H:i:s', 'message' => Yii::t('app', 'SUBMIT_TIME_ADV_IS_NOT_DATETIME')],

            // open_contacts_days
            ['open_contacts_days', 'required', 'on' => self::SCENARIO_UPDATE_OPEN_CONTACTS],
            ['open_contacts_days', 'integer', 'except' => self::SCENARIO_UPDATE_OPEN_CONTACTS],
            ['open_contacts_days', 'integer', 'min' => 1, 'max' => 7,
                'on' => self::SCENARIO_UPDATE_OPEN_CONTACTS,
            ],

            // open_contacts_expiry
            ['open_contacts_expiry', 'required', 'on' => self::SCENARIO_UPDATE_OPEN_CONTACTS],
            ['open_contacts_expiry', 'integer', 'min' => 0],
        ];
    }

    public function getEntityType()
    {
        return self::ENTITY_TYPE_LOAD;
    }

    /**
     * Validates loading date
     * Loading date must be equal or greater than today.
     *
     * @param string $attribute Loading date
     * @param array $params The value of the "params" given in the rule
     * @return boolean
     */
    public function validateDate($attribute, $params = [])
    {
        $today = date('Y-m-d');
        if ($this->date < $today) {
            $this->addError($attribute, isset($params['message']) ? $params['message'] : null);
        }
        return true;
    }

    /**
     * Validates not logged-in user email
     * Email is valid when there is no active load in current moment with this email
     *
     * @param string $attribute Name of the attribute that is being validated
     * @param array $params The value of the "params" given in the rule
     * @return boolean
     */
    public function validateEmail($attribute, $params = [])
    {
        if ($this->existsByEmail()) {
            $this->addError($attribute, isset($params['message']) ? $params['message'] : null);
        }
        return true;
    }

    /**
     * Validates date range attributes
     *
     * @param string $attribute Date range attribute name
     * @param array $params The value of the "params" given in the rule
     * @return boolean
     */
    public function validateDateRange($attribute, $params = [])
    {
        if (empty($this->dateFrom) || empty($this->dateTo)) {
            $this->addError($attribute, isset($params['emptyMessage']) ? $params['emptyMessage'] : '');
            return true;
        }

        if ($this->dateFrom > $this->dateTo) {
            $this->addError($attribute, isset($params['invalidMessage']) ? $params['invalidMessage'] : '');
            return true;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => Yii::t('app', 'LOAD_USER_ID_LABEL'),
            'code' => Yii::t('app', 'LOAD_CODE_LABEL'),
            'type' => Yii::t('app', 'LOAD_TYPE_LABEL'),
            'payment_method' => Yii::t('app', 'LOAD_PAYMENT_METHOD_LABEL'),
            'date' => Yii::t('app', 'LOAD_DATE_LABEL'),
            'price' => Yii::t('app', 'LOAD_PRICE_LABEL'),
            'open_contacts_days' => Yii::t('app', 'LOAD_OPEN_CONTACTS_DAYS_LABEL'),
            'open_contacts_expiry' => Yii::t('app', 'LOAD_OPEN_CONTACTS_EXPIRY_LABEL'),
            'status' => Yii::t('app', 'LOAD_STATUS_LABEL'),
            'active' => Yii::t('app', 'LOAD_ACTIVE_LABEL'),
            'transported' => Yii::t('app', 'LOAD_TRANSPORTED_LABEL'),
            'date_of_expiry' => Yii::t('app', 'LOAD_DATE_OF_EXPIRY_LABEL'),
            'token' => Yii::t('app', 'LOAD_TOKEN_LABEL'),
            'phone' => Yii::t('app', 'LOAD_PHONE_LABEL'),
            'email' => Yii::t('app', 'LOAD_EMAIL_LABEL'),
            'created_at' => Yii::t('app', 'LOAD_CREATED_AT_LABEL'),
            'updated_at' => Yii::t('app', 'LOAD_UPDATED_AT_LABEL'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLoadCars()
    {
        return $this->hasMany(LoadCar::className(), ['load_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLoadCities()
    {
        return $this->hasMany(LoadCity::className(), ['load_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLoadPreviews()
    {
        return $this->hasMany(LoadPreview::className(), ['load_id' => 'id']);
    }

    /**
     * Checks whether user with this email has already has active load
     *
     * @return boolean
     */
    public function existsByEmail()
    {
        return self::find()
            ->where([
                'email' => $this->email,
                'status' => self::ACTIVE,
            ])
            ->andWhere(['>', 'date_of_expiry', time()])
            ->exists();
    }

    /**
     * Returns load activities
     *
     * @return array
     */
    public static function getActivities()
    {
        return [self::NOT_ACTIVATED, self::ACTIVATED];
    }

	/**
     * Returns load activities
     *
     * @return array
     */
    public static function getLoadListActivities()
    {
        return [
            self::ACTIVE => Yii::t('element', 'MK-C-22a'),
            self::NOT_ACTIVATED => Yii::t('element', 'MK-C-22b'),
            self::BOTH => Yii::t('element', 'MK-C-22c'),];
    }

    /**
     * Returns user load data
     *
     * @param null|string $token Not logged-in or signed-in user identification string
     * @param array $ids Load/unload cities IDs
     * @return ActiveDataProvider
     */
    public static function getMyLoadsDataProvider($token = null, $ids = [])
    {
		switch (Yii::$app->request->get('load-activity', Load::ACTIVE)) {
            case Load::ACTIVE:
                $condition = ['>', self::tableName() . '.date_of_expiry', time()];
                break;
            case Load::NOT_ACTIVATED:
                $condition = ['<', self::tableName() . '.date_of_expiry', time()];
                break;
            case Load::BOTH:
                $condition = '';
                break;
            default:
                $condition = ['>', self::tableName() . '.date_of_expiry', time()];
        }
        $query = self::find()
            ->joinWith('loadCities')
            ->joinWith('loadCities.city')
            ->joinWith('loadCars')
            ->where([self::tableName() . '.`status`' => self::ACTIVE])
            ->andWhere(['token' => Yii::$app->user->isGuest ? $token : self::DEFAULT_TOKEN])
            ->andWhere(['user_id' => Yii::$app->user->isGuest ? self::DEFAULT_USER_ID : Yii::$app->user->id])
			->andWhere($condition)
            ->andFilterWhere(['or',
                [LoadCity::tableName() . '.`city_id`' => $ids],
                [City::tableName() . '.`country_code`' => City::findCountriesCountryCode($ids)],
            ])
            ->groupBy(self::tableName() . '.`id`')
            ->orderBy([self::tableName() . '.`created_at`' => SORT_DESC]);

//        self::updateExpiredLoadStatus(clone $query);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'page' => (Yii::$app->request->get('load-page') - 1),
                'pageParam' => 'load-page',
				'params' => [
                    'load-activity' => Yii::$app->request->get('load-activity', Load::ACTIVE),
                    'loadCities' => Yii::$app->request->get('loadCities'),
                ],
                'pageSize' => Yii::$app->request->get('load-per-page'),
                'defaultPageSize' => self::FIRST_PAGE_SIZE,
                'pageSizeParam' => 'load-per-page',
                'route' => 'my-announcement/index',
            ],
        ]);

        return $dataProvider;
    }

    /**
     * Returns all translated available payment methods
     *
     * @return array
     */
    public static function getTranslatedPaymentMethods()
    {
        return [
            self::FOR_CAR_MODEL => Yii::t('element', 'IA-C-48a'),
            self::FOR_ALL_LOAD => Yii::t('element', 'IA-C-48b'),
        ];
    }

    /**
     * Finds active load information by load ID and joins it with user table
     *
     * @param null|integer $id Load ID
     * @return array|null|ActiveRecord
     */
    public static function findByLoadIdWithUser($id = null)
    {
        return self::find()
            ->joinWith('user')
            ->where([
                self::tableName() . '.id' => $id,
                self::tableName() . '.status' => self::ACTIVE,
            ])
            ->one();
    }

    /**
     * Checks whether announced load is expired
     *
     * @return boolean
     */
    public function isExpired()
    {
        return $this->date_of_expiry < time();
    }

    /**
     * If announced load is expired, extends expiry date to 7 weekdays from today
     */
    public function extendExpiryDate()
    {
        $this->date_of_expiry = strtotime("+" . self::DAYS_TO_EXPIRE . " weekdays");
    }

    /**
     * Creates new load entry
     *
     * @return boolean Whether entry created successfully
     */
    public function create()
    {
        if (!$this->validate()) {
            return false;
        }
        $date = strtotime($this->date);
        $this->user_id = Yii::$app->user->isGuest ? self::DEFAULT_USER_ID : Yii::$app->user->id;
        $this->code = self::DEFAULT_CODE;
        $this->date = ($date ? $date : 0);
        $this->price = ($this->isPaymentMethodForCarModel()) ? self::DEFAULT_PRICE : str_replace(',', '.', $this->price);
        $this->status = self::ACTIVE;
        $this->active = self::ACTIVATED;
        $this->transported = self::DEFAULT_TRANSPORTED;
        $this->extendExpiryDate();
        $this->token = Yii::$app->user->isGuest ? $this->getGeneratedToken() : self::DEFAULT_TOKEN;
        $this->phone = Yii::$app->user->isGuest ? $this->phone : self::DEFAULT_PHONE;
        $this->email = Yii::$app->user->isGuest ? $this->email : self::DEFAULT_EMAIL;
        $this->scenario = self::SCENARIO_ANNOUNCE_SERVER;
        return $this->save();
    }

    /**
     * Updates load code
     *
     * @param string $code Load code that needs to be saved to database
     * @return false|integer The number of affected rows, or false if validation fails
     */
    public function updateCode($code = '')
    {
        $this->scenario = self::SCENARIO_UPDATE_CODE;
        $this->code = empty($code) ? $this->getGeneratedCode() : '';
        return $this->update(true, ['code']);
    }

    /**
     * Generates and returns new load code
     *
     * @return string
     */
    private function getGeneratedCode()
    {
        return self::LOAD_CODE_SYMBOL . date('Y') . '-' . date('m') . '-' . sprintf("%'.06d", $this->id);
    }

    /**
     * Generates and returns load token
     *
     * @param integer $length Token length
     * @return string
     */
    private function getGeneratedToken($length = self::TOKEN_LENGTH)
    {
        return Yii::$app->security->generateRandomString($length);
    }

    /**
     * Sends mail with link to "My loads" page
     *
     * @return boolean Whether mail was sent successfully
     */
    public function sendMyLoadsLink()
    {
        if (!Yii::$app->user->isGuest && $this->user_id !== self::DEFAULT_USER_ID) {
            return true;
        }

        $isSentSuccessfully = Yii::$app->mailer->compose('load/my-loads', [
            'url' => $this->getMyLoadsLink(),
            'companyName' => Yii::$app->params['companyName'],
        ])
            ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->params['companyName']])
            ->setTo($this->email)
            ->setSubject(Yii::t('mail', 'LOAD_MY_LOADS_SUBJECT', [
                'companyName' => Yii::$app->params['companyName'],
            ]))
            ->send();

        if ($isSentSuccessfully) {
            Log::user(SystemMessage::ACTION, SystemMessage::PLACEHOLDER_USER_RECEIVED_EMAIL_FOR_ANNOUNCING_LOAD, []);
        }

        return $isSentSuccessfully;
    }

    /**
     * Returns link to "My loads" page
     *
     * @return string
     */
    private function getMyLoadsLink()
    {
        return Url::to([
            'my-announcement/index',
            'lang' => Yii::$app->language,
            'token' => $this->token,
        ], true);
    }

    /**
     * Returns translated load types
     *
     * @return array
     */
    public static function getTranslatedTypes()
    {
        return [
            Load::TYPE_FULL => Yii::t('element', 'IA-C-2'),
            Load::TYPE_PARTIAL => Yii::t('element', 'IA-C-3'),
        ];
    }

    /**
     * Returns list of page sizes
     *
     * @return array
     */
    public static function getPageSizes()
    {
        return [
            self::FIRST_PAGE_SIZE => '1-' . self::FIRST_PAGE_SIZE,
            self::SECOND_PAGE_SIZE => '1-' . self::SECOND_PAGE_SIZE,
            self::THIRD_PAGE_SIZE => '1-' . self::THIRD_PAGE_SIZE,
        ];
    }

    /**
     * Checks whether load has cars
     *
     * @return array|LoadCar[]
     */
    public function checkLoadCarsExistence()
    {
        if (isset($this->loadCars) && !empty($this->loadCars)) {
            return $this->loadCars;
        }
        return [new LoadCar(['scenario' => LoadCar::SCENARIO_EDIT_CAR_INFO])];
    }

    /**
     * Checks whether load type is partial
     *
     * @return boolean
     */
    public function isTypePartial()
    {
        return $this->type == self::TYPE_PARTIAL;
    }

    /**
     * Checks whether load type is full
     *
     * @return boolean
     */
    public function isTypeFull()
    {
        return $this->type == self::TYPE_FULL;
    }

    /**
     * Checks whether load payment method is for car model
     *
     * @return boolean
     */
    public function isPaymentMethodForCarModel()
    {
        return $this->payment_method == self::FOR_CAR_MODEL;
    }

    /**
     * Checks whether payment method is for all load
     *
     * @return boolean
     */
    public function isPaymentMethodForAllLoad()
    {
        return $this->payment_method == self::FOR_ALL_LOAD;
    }

    /**
     * Checks whether load is activated
     *
     * @return boolean
     */
    public function isActivated()
    {
        return $this->active == self::ACTIVATED;
    }

    /**
     * Returns list of load search radius
     *
     * @return array
     */
    public static function getSearchRadius()
    {
        return [
            self::FIRST_RADIUS => self::FIRST_RADIUS . ' km',
            self::SECOND_RADIUS => self::SECOND_RADIUS . ' km',
            self::THIRD_RADIUS => self::THIRD_RADIUS . ' km',
        ];
    }

    /**
     * Finds all active loads by given list of load IDs
     *
     * @return static[]
     */
    public static function findAllActiveByIds()
    {
        return self::find()
            ->joinWith('loadCars')
            ->joinWith('loadCities')
            ->joinWith('loadCities.city')
            ->joinWith('user.companies')
            ->where([
                self::tableName() . '.status' => self::ACTIVE,
                self::tableName() . '.active' => self::ACTIVATED,
            ])
            ->andWhere(['or',
                [
                    User::tableName() . '.active' => User::ACTIVE,
                    User::tableName() . '.allow' => User::ALLOWED,
                    User::tableName() . '.archive' => User::NOT_ARCHIVED,
                ],
                [self::tableName() . '.user_id' => null],
            ])
            ->andWhere(['>', self::tableName() . '.date_of_expiry', time()]);
            //->all();/* Comment this line to generate query. */
    }

    /**
     * Returns all search results loads models
     *
     * @return static[]
     */
    public static function getSearchResultsLoads()
    {
        $loads = self::findAllActiveByIds();

        return $loads;
    }

    /**
     * Returns all loads with active company or active quest's loads
     *
     * @param array $activeLoadsIds list of active copmanies or quests loads
     * @param array $allLoadsIds list of all loads
     * @return static[]
     */
    public static function removeDirectLoadsWithInactiveCompanies($activeLoadsIds, $allLoadsIds)
    {
        $loads = [];
        foreach($activeLoadsIds as $activeLoadId) {
            foreach($allLoadsIds as $allLoadsId) {
                if ($activeLoadId->id == key($allLoadsId)) {
                    array_push($loads, $allLoadsId);
                }
            }
        }
        return $loads;
    }

    /**
     * Returns all loads with active company or active quest's loads
     *
     * @param array $activeLoadsIds list of active companies or quests loads
     * @param array $allLoadsIds list of all loads
     * @return static[]
     */
    public static function removeFullLoadsWithInactiveCompanies($activeLoads, $allLoadsIds)
    {
        $loads = [];
        foreach($allLoadsIds as $load) {
            $count = 0;
            foreach($load as $loadId) {
                foreach($activeLoads as $activeLoad) {
                    if ($activeLoad->id == $loadId) {
                        $count++;
                    }
                    if ($count >= self::MATCH_NUMBER) {
                        array_push($loads, $load);
                        $count = 0;
                        break;
                    }
                }
            }
        }
        return $loads;
    }

    /**
     * Returns all loads with active company or active quest's loads
     *
     * @param array $activeLoadsIds list of active companies or quests loads
     * @param array $allLoadsIds list of all loads
     * @return static[]
     */
    public static function removeRoundtripsWithInactiveCompanies($activeLoads, $allLoadsIds)
    {
        $loads = [];
        foreach($allLoadsIds as $load) {
            $count = 0;
            foreach($load as $loadId) {
                foreach($activeLoads as $activeLoad) {
                    if ($activeLoad->id == $loadId) {
                        $count++;
                    }
                    if ($count == count($load)) {
                        array_push($loads, $load);
                        $count = 0;
                        break;
                    }
                }
            }
        }
        return $loads;
    }

    /**
     * Returns all loads with active company or active quest's loads
     *
     * @param array $activeLoadsIds list of active companies or quests loads
     * @param array $allLoadsIds list of all loads
     * @return static[]
     */
    public static function removeInactive($activeLoads, $allLoadsIds)
    {
        $loads = [];
        foreach($allLoadsIds as $id => $load) {
            switch ($id) {
                case 'direct':
                    $loads[$id] = self::removeDirectLoadsWithInactiveCompanies($activeLoads, $load);
                    break;
                case 'additional':
                    $loads[$id] = self::removeFullLoadsWithInactiveCompanies($activeLoads, $load);
                    break;
                case 'fullUnload':
                    $loads[$id] = self::removeFullLoadsWithInactiveCompanies($activeLoads, $load);
                    break;
            }
        }
        return $loads;
    }

    /**
     * Adds direct transportation loads IDs to the common list of loads IDs
     *
     * @param array $ids Common list of loads IDs
     * @param array $directs Direct transportation loads IDs
     */
    private static function addDirectIds(&$ids = [], $directs = [])
    {
        foreach ($directs as $direct) {
            if (is_array($direct)) {
                foreach ($direct as $id => $content) {
                    if (!in_array($id, $ids)) {
                        array_push($ids, $id);
                    }
                }
            }
        }
    }

    /**
     * Adds devious transportation loads IDs to the common list of loads IDs
     *
     * @param array $ids Common list of loads IDs
     * @param array $devious Devious transportation loads IDs
     */
    private static function addDeviousIds(&$ids = [], $devious = [])
    {
        foreach ($devious as $group) {
            foreach ($group as $id) {
                if (!in_array($id, $ids)) {
                    array_push($ids, $id);
                }
            }
        }
    }

    /**
     * Returns list of load models for suggestions
     *
     * @return static[]
     */
    public static function getSuggestionsLoads()
    {
        $loads = self::findAllActiveByIds();
        return $loads;
    }

    /**
     * Supplements suggestions loads IDs with new loads IDs
     *
     * @param array $ids List of suggestions loads IDs
     * @param array $loads List of searches results with loads IDs
     */
    private static function supplementSuggestionsLoadsIds(&$ids = [], $loads = [])
    {
        $directLoads = $loads['direct'];
        $additionalLoads = $loads['additional'];
        $fullUnloadLoads = $loads['fullUnload'];

        self::addDirectIds($ids, $directLoads);
        self::addDeviousIds($ids, $additionalLoads);
        self::addDeviousIds($ids, $fullUnloadLoads);
    }

    /**
     * Returns list of loads suggestions types
     *
     * @return array
     */
    public static function getSuggestionsTypes()
    {
        return [
            self::DEFAULT_SUGGESTIONS_TYPE => Yii::t('app', 'DEFAULT_SUGGESTIONS'),
            self::DIRECT_SUGGESTIONS => Yii::t('app', 'DIRECT_SUGGESTIONS'),
            self::ADDITIONAL_SUGGESTIONS => Yii::t('app', 'ADDITIONAL_SUGGESTIONS'),
            self::FULL_UNLOAD_SUGGESTIONS => Yii::t('app', 'FULL_UNLOAD_SUGGESTIONS'),
        ];
    }

    /**
     * Returns round trips models
     *
     * @param array $ids List of round trips loads IDs
     * @return array|ActiveRecord[]
     */
    public static function getRoundTripsModels($ids = [])
    {
        $loads = self::find()
            ->joinWith('loadCars')
            ->joinWith('loadCities')
            ->joinWith('loadCities.city')
            ->joinWith('user.companies')
            ->where([
                self::tableName() . '.id' => $ids,
                self::tableName() . '.status' => self::ACTIVE,
                self::tableName() . '.active' => self::ACTIVATED,
            ])
            ->andWhere(['or',
                [
                    User::tableName() . '.active' => User::ACTIVE,
                    User::tableName() . '.allow' => User::ALLOWED,
                    User::tableName() . '.archive' => User::NOT_ARCHIVED,
                ],
                [self::tableName() . '.user_id' => null],
            ])
            ->andWhere(['>', self::tableName() . '.date_of_expiry', time()])
            ->all();

        return $loads;
    }

    /**
     * Finds all active loads
     *
     * @return array|self[]
     */
    public static function findAllActive()
    {
        $loads = self::find()
            ->joinWith('loadCars')
            ->joinWith('loadCities')
            ->joinWith('loadCities.city')
            ->joinWith('user.companies')
            ->where([
                self::tableName() . '.status' => self::ACTIVE,
                self::tableName() . '.active' => self::ACTIVATED,
            ])
            ->andWhere(['or',
                [
                    User::tableName() . '.active' => User::ACTIVE,
                    User::tableName() . '.allow' => User::ALLOWED,
                    User::tableName() . '.archive' => User::NOT_ARCHIVED,
                ],
                [self::tableName() . '.user_id' => null],
            ])
            ->andWhere(['>', self::tableName() . '.date_of_expiry', time()])
            ->all();

        return $loads;
    }
    
    /**
     * Marks unmarked inactive or expired loads with at least one preview
     */
    public static function markTransported()
    {
        $subquery = (new Query)
            ->select('load1.id AS id')
            ->from(['load1' => self::find()])
            ->leftJoin(LoadCar::tableName(), LoadCar::tableName() . '.`load_id` = `load1`.`id`')
            ->rightJoin(User::tableName(), User::tableName() . '.`id` = `load1`.`user_id`')
            ->leftJoin(LoadPreview::tableName(), LoadPreview::tableName() . '.`id` = `load1`.`user_id`')
            ->where(['IS NOT', LoadPreview::tableName() . '.id', null])
            ->andWhere(['load1.transported' => self::TRANSPORTED_NO])
            ->andWhere(['or',
                [
                    'load1.status' => self::INACTIVE,
                    'load1.active' => self::NOT_ACTIVATED,
                ],
                [
                    '<', 'load1.date_of_expiry', time()
                ],
            ])
            ->andWhere(['or',
                [
                    User::tableName() . '.active' => User::ACTIVE,
                    User::tableName() . '.allow' => User::ALLOWED,
                    User::tableName() . '.archive' => User::NOT_ARCHIVED,
                ],
                ['load1.user_id' => null],
            ])
            ->distinct();
        
        self::updateAll(['transported' => self::TRANSPORTED_YES], ['in', 'id', $subquery]);
    }
    
    /**
     * Marks expired loads without previews
     */
    public static function markTransportedNoPreviews()
    {
        $subquery = (new Query)
            ->select('load1.id AS id')
            ->from(['load1' => self::find()])
            ->leftJoin(LoadCar::tableName(), LoadCar::tableName() . '.`load_id` = `load1`.`id`')
            ->leftJoin(LoadPreview::tableName(), LoadPreview::tableName() . '.`id` = `load1`.`user_id`')
            ->where(['IS', LoadPreview::tableName() . '.id', null])
            ->andWhere(['load1.transported' => self::TRANSPORTED_NO])
            ->andWhere(['or',
                [
                    'load1.status' => self::INACTIVE,
                    'load1.active' => self::NOT_ACTIVATED,
                ],
                [
                    '<', 'load1.date_of_expiry', time()
                ],
            ])
            ->distinct();
        
        self::updateAll(['transported' => self::TRANSPORTED_NO_PREVIEWS], ['in', 'id', $subquery]);
    }
    
    /**
     * Returns sum of loads marked as transported
     */
    public static function getTransportedTotal()
    {        
        $loadCarQuantities = LoadCar::find()
            ->select('SUM(' . LoadCar::tableName() . '.`quantity`)')
            ->where(LoadCar::tableName() . '.`load_id` = `t1`.`id`');
        
        $loadCarQuantitiesSql = $loadCarQuantities->createCommand()->getRawSql();
        
        $loadsSubquery = (new Query())
            ->select("CASE WHEN `t1`.`type` = " . self::TYPE_FULL . 
                " THEN " . self::TYPE_LIMIT . 
                " ELSE COALESCE((" . $loadCarQuantitiesSql . "), 0) END AS `qty`"
            )->from(Load::tableName() . ' AS t1')
            ->where("`t1`.`transported` = " . self::TRANSPORTED_YES);
        
        $query = (new Query())
            ->select('SUM(`t2`.`qty`)')
            ->from(['t2' => $loadsSubquery]);
        
        $transportedTotal = $query->scalar();
        
        return $transportedTotal;
    }

    /**
     * Counts how many cars have load
     *
     * @return integer
     */
    public function countCars()
    {
        $quantities = 0;
        
        foreach($this->loadCars as $loadCar) {
            $quantities += $loadCar->quantity;
        }
        
        return is_null($quantities) ? 0 : $quantities;
    }

    /**
     * Deactivates all active loads
     *
     * @return integer Number of deactivated loads
     */
    public static function deactivateAllActivated()
    {
        return self::updateAll(['active' => self::NOT_ACTIVATED], [
            'status' => self::ACTIVE,
            'active' => self::ACTIVATED,
            'transported' => self::TRANSPORTED_NO,
        ]);
    }

    /**
     * Removes old loads
     *
     * @param integer $term Number of days that load considered as old
     * @return integer Number of removed old loads
     */
    public static function removeOld($term = 0)
    {
        $limit = strtotime('-' . $term . ' days');
        $rows = self::updateAll([
            'status' => self::INACTIVE,
        ], 'status = :status AND date_of_expiry <= :limit', [
            ':status' => self::ACTIVE,
            ':limit' => $limit,
        ]);
        
        self::markTransportedNoPreviews();
        return $rows;
    }

    /**
     * Checks whether current user can announce load
     *
     * @param boolean $isGuest Whether current user is guest
     * @return boolean
     */
    public function canAnnounceLoad($isGuest = false)
    {
        return $isGuest ? $this->canGuestAnnounceLoad() : $this->canLoggedInUserAnnounceLoad();
    }

    /**
     * Checks whether logged-in user can announce load
     *
     * @return boolean
     */
    private function canLoggedInUserAnnounceLoad()
    {
        $activeServices = UserServiceActive::findAllActiveUserServices();
        if (!empty($activeServices)) {
            return true;
        }

        // User does not have active subscription, so we need to check if it has active loads
        $loads = self::findByUserId(Yii::$app->user->id);
        $can = !$this->hasNotExpiredLoads($loads);

        return $can;
    }

    /**
     * Finds loads that announced specific user
     *
     * @param null $userId Loads owner ID
     * @return array|ActiveRecord[]
     */
    private function findByUserId($userId = null)
    {
        $user = User::find()
                ->where(['id' => $userId])
                ->one();
        return self::find()
            ->where(['or',
                ['user_id' => $userId],
                ['email' => $user->email]
            ])
            ->andWhere(['>', 'date_of_expiry', time()])
            ->all();
    }

    /**
     * Checks whether guest can announce load
     *
     * @return boolean
     */
    private function canGuestAnnounceLoad()
    {
        $condition = [];
        $user = User::find()
                ->where(['email' => $this->email])
                ->one();
        if(!empty($user)) {
            $condition = ['user_id' => $user->id];
        }
        $startDate = strtotime("-1 week"); // User can announce one load per week
        $endDate = time();
        $loads = self::find()
            ->where(['or',
                ['email' => $this->email],
                $condition
            ])
            ->andWhere(['between', 'created_at', $startDate, $endDate])
            ->all();

        return empty($loads); // User did not announce any load in whole week
    }

    /**
     * Checks whether loads have at least one load that is not expired
     *
     * @param self[] $loads List of loads
     * @return boolean
     */
    private function hasNotExpiredLoads($loads = [])
    {
        $hasNotExpired = false;
        foreach ($loads as $load) {
            if ($load->date_of_expiry > time()) {
                $hasNotExpired = true;
            }
        }

        return $hasNotExpired;
    }

    /**
     * Returns loads data provider for administration loads filtration
     *
     * @param LoadCity $loadCity Load city model
     * @param null|integer $id Company ID
     * @return ActiveDataProvider
     */
    public function getAdminDataProvider(LoadCity $loadCity, $id = null)
    {
        $previewTableName = LoadPreview::tableName();
        $loadTableName = self::tableName();

        $query = self::find()
            ->joinWith('loadCities')
            ->joinWith('loadCities.city')
            ->joinWith('loadCars')
            ->joinWith('loadPreviews')
            ->joinWith('user')
            ->joinWith('user.companies ownerCompany')
            ->joinWith('user.companyUser')
            ->joinWith('user.companyUser.company userCompany')
            ->addSelect(["COUNT({$previewTableName}.id) as preview_count", "{$loadTableName}.*"])
            ->andFilterWhere([
                'or',
                ['ownerCompany.id' => $id],
                ['userCompany.id' => $id],
            ])
            ->groupBy([self::tableName() . '.id']);

        $query = $this->filterDate($query);
        $query = $this->filterCities($query, $loadCity);

        if (array_key_exists($this->type, self::getTranslatedTypes())) {
            $query->andFilterWhere([self::tableName() . '.type' => $this->type]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'active' => [
                        'default' => SORT_DESC,
                    ],
                    'preview_count' => [
                        'default' => SORT_DESC,
                    ],
                    'created_at' => [
                        'default' => SORT_DESC,
                    ]
                ],
            ],
        ]);

        return $dataProvider;
    }

    /**
     * Returns loads data provider for administration loads filtration
     *
     * @param LoadCity $loadCity Load city model
     * @param null|integer $id Company ID
     * @param int $pageSize
     * @return ActiveDataProvider
     */
    public function companyLoadsDataProvider(LoadCity $loadCity, $id = null, $pageSize = self::FIRST_PAGE_SIZE)
    {
        $query = self::find()
            ->joinWith('loadCities')
            ->joinWith('loadCities.city')
            ->joinWith('loadCars')
            ->joinWith('loadPreviews')
            ->joinWith('user')
            ->joinWith('user.companies ownerCompany')
            ->joinWith('user.companyUser')
            ->joinWith('user.companyUser.company userCompany')
            ->andFilterWhere([
                'or',
                ['ownerCompany.id' => $id],
                ['userCompany.id' => $id],
            ])
            ->groupBy([self::tableName() . '.id'])
            ->orderBy([self::tableName() . '.created_at' => SORT_DESC]);


        $query = $this->filterDate($query);
        $query = $this->filterCities($query, $loadCity);

        if (array_key_exists($this->type, self::getTranslatedTypes())) {
            $query->andFilterWhere([self::tableName() . '.type' => $this->type]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'page' => (Yii::$app->request->get('load-page') - 1),
                'pageSizeParam' => 'per-load-page',
                'pageParam' => 'load-page',
                'params' => ['tab' => ClientController::TAB_COMPANY_LOADS, 'id' => Yii::$app->request->get('id')],
                'pageSize' => Yii::$app->request->get('per-load-page'),
                'defaultPageSize' => self::FIRST_PAGE_SIZE,
                'route' => 'client/company',
            ],
        ]);

        return $dataProvider;
    }

    /**
     * Filters load date
     *
     * @param ActiveQuery $query Load query
     * @return ActiveQuery
     */
    private function filterDate(ActiveQuery $query)
    {
        if (!empty($this->dateFrom)) {
            $dateFrom = self::convertDateToTimestamp($this->dateFrom . ' 00:00:00 Europe/Vilnius');
        } else {
            $dateFrom = self::convertDateToTimestamp($this->dateFrom);
        }

        if (!empty($this->dateFrom)) {
            $dateTo = self::convertDateToTimestamp($this->dateTo . ' 23:59:59 Europe/Vilnius');
        } else {
            $dateTo = self::convertDateToTimestamp($this->dateTo);
        }

        if (is_null($dateFrom) && is_null($dateTo)) {
            $query->andWhere(['>', self::tableName() . '.date_of_expiry', time()]);
			$query->andWhere(['or',
                [
                    User::tableName() . '.active' => User::ACTIVE,
                    User::tableName() . '.allow' => User::ALLOWED,
                    User::tableName() . '.archive' => User::NOT_ARCHIVED,
                ],
                [self::tableName() . '.user_id' => null],
            ]);
            return $query;
        }

        $query->andFilterWhere(['between', self::tableName() . '.created_at', $dateFrom, $dateTo]);
        return $query;
    }

    /**
     * Converts provided string date to timestamp
     *
     * @param string $date Date in string format
     * @return false|integer|null Timestamp or false|null otherwise
     */
    private static function convertDateToTimestamp($date)
    {
        return !empty($date) && is_string($date) ? strtotime($date) : null;
    }

    /**
     * Filters load cities
     *
     * @param ActiveQuery $query Load query
     * @param LoadCity $loadCity Load city model
     * @return ActiveQuery
     */
    private function filterCities(ActiveQuery $query, LoadCity $loadCity)
    {
        $loadCityId = self::getFiltrationCity($loadCity->loadCityId, $loadCity->loadCountry);
        $unloadCityId = self::getFiltrationCity($loadCity->unloadCityId, $loadCity->unloadCountry);

        if (!empty($loadCityId) && !empty($unloadCityId)) {
            $loadCityQuery = LoadCity::find()->andWhere([
                LoadCity::tableName() . '.city_id' => $loadCityId,
                LoadCity::tableName() . '.type' => LoadCity::LOADING,
            ]);

            $on = Load::tableName() . '.id = ' . LoadCity::tableName() . '.load_id';
            $query->leftJoin(['loadCity' => $loadCityQuery], $on);
            $query->andWhere('loadCity.load_id = ' . LoadCity::tableName() . '.load_id');
            $query->andWhere([
                LoadCity::tableName() . '.city_id' => $unloadCityId,
                LoadCity::tableName() . '.type' => LoadCity::UNLOADING,
            ]);

            return $query;
        }

        if (!empty($loadCityId)) {
            $query->andWhere([
                LoadCity::tableName() . '.city_id' => $loadCityId,
                LoadCity::tableName() . '.type' => LoadCity::LOADING,
            ]);
        }

        if (!empty($unloadCityId)) {
            $query->andWhere([
                LoadCity::tableName() . '.city_id' => $unloadCityId,
                LoadCity::tableName() . '.type' => LoadCity::UNLOADING,
            ]);
        }

        return $query;
    }

    /**
     * Returns filtration city or cities
     *
     * @param null|integer $cityId Load/unload city ID
     * @param null|string $countryCode Load/unload country code
     * @return array|string
     */
    private static function getFiltrationCity($cityId, $countryCode)
    {
        if (!empty($cityId)) {
            return $cityId;
        }

        if (!empty($countryCode)) {
            return City::findCountryCitiesIds($countryCode);
        }

        return '';
    }

    /**
     * Adds timezone offset to date
     *
     * @param string $attribute Date attribute name
     * @return integer
     */
    public function addDateOffset($attribute)
    {
        $timestamp = $this->$attribute;
        if (empty($timestamp)) {
            return self::DATE_NOT_SET;
        }

        $hours = (integer) Yii::$app->session->get('timezone-offset');
        $seconds = $hours * 3600;
        $offset = $timestamp + $seconds;

        return $offset;
    }

    /**
     * Converts provided timestamp to text or simple date format, depending on timestamp value
     *
     * @see http://stackoverflow.com/a/4186922/5747867
     * @param integer $timestamp Target timestamp
     * @param boolean $showTime Whether to show time
     * @return string
     */
    public static function convertTimestampToDateText($timestamp, $showTime = true)
    {
        $dateTime = new DateTime('@' . $timestamp); // NOTE: will snap to UTC because of the "@timezone" syntax
        $date = $dateTime->format('Y-m-d');
        $time = ($showTime ? $dateTime->format('H:i') : '');

        $dateTime = new DateTime('@' . time());
        $today = $dateTime->format('Y-m-d');

        switch (self::calculateDatesDifference($today, $date)) {
            case 0: // Today
                return Yii::t('element', 'A-C-326a') . " $time";
            case 1: // Yesterday
                return Yii::t('element', 'A-C-326b') . " $time";
            default: // Any other day
                return "$date $time";
        }
    }

    /**
     * Calculates days difference between two dates
     *
     * @param string $firstDate First date in string format
     * @param string $secondDate Second date in string format
     * @return float|integer
     */
    private static function calculateDatesDifference($firstDate, $secondDate)
    {
        $firstTimestamp = strtotime($firstDate);
        $secondTimestamp = strtotime($secondDate);
        $subtraction = $firstTimestamp - $secondTimestamp;
        $dayInSeconds = (60 * 60 * 24);
        $difference = $subtraction / $dayInSeconds;

        return $difference;
    }

    /**
     * Checks whether current suggestion type is default
     *
     * @return boolean
     */
    public function isDefaultSuggestion()
    {
        return $this->suggestionsType == self::DEFAULT_SUGGESTIONS_TYPE;
    }

    /**
     * Checks whether current suggestion type is direct
     *
     * @return boolean
     */
    public function isDirectSuggestion()
    {
        return $this->suggestionsType == self::DIRECT_SUGGESTIONS;
    }

    /**
     * Checks whether current suggestion type is additional
     *
     * @return boolean
     */
    public function isAdditionalSuggestion()
    {
        return $this->suggestionsType == self::ADDITIONAL_SUGGESTIONS;
    }

    /**
     * Checks whether current suggestion type is full unload
     *
     * @return boolean
     */
    public function isFullUnloadSuggestion()
    {
        return $this->suggestionsType == self::FULL_UNLOAD_SUGGESTIONS;
    }

    /**
     * Checks whether given date is the same as current load date
     *
     * @param string $date Target date
     * @return boolean
     */
    public function hasSameDate($date)
    {
        return $this->date === strtotime($date);
    }

    /**
     * Sets load price depending on load type
     */
    public function setPrice()
    {
        $this->price = $this->isPaymentMethodForAllLoad() ? str_replace(',', '.', $this->price) : self::DEFAULT_PRICE;
    }

    /**
     * Returns translated load active statuses
     *
     * @return array
     */
    public static function getTranslatedActiveStatus()
    {
        return [
            self::NOT_ACTIVATED => Yii::t('app', 'NOT_ACTIVATED_LOAD'),
            self::ACTIVATED => Yii::t('app', 'ACTIVATED_LOAD'),
        ];
    }

    /**
     * Returns translated load statuses
     *
     * @return array
     */
    public static function getTranslatedStatuses()
    {
        return [
            self::INACTIVE => Yii::t('app', 'INACTIVE_LOAD'),
            self::ACTIVE => Yii::t('app', 'ACTIVE_LOAD'),
        ];
    }

    /**
     * Hide sugesstion for current session
     *
     * @param array $idArray load ID array
     */
    public static function hideSuggestion($idArray)
    {
        $session = Yii::$app->session;
        $suggestions = self::getHiddenSuggestions();
        $suggestions[] = $idArray;
        $session->set(self::HIDDEN_SUGGESTIONS, $suggestions);
    }

    /**
     * Get hidden load suggestion id list from session
     *
     * @return array
     */
    public static function getHiddenSuggestions()
    {
        $session = Yii::$app->session;
        if ($session->has(self::HIDDEN_SUGGESTIONS)) {
            return $session->get(self::HIDDEN_SUGGESTIONS);
        }

        return [];
    }

    /**
     * Removes hidden loads that match id array saved in session
     *
     * @param array $typedLoads with keys 'direct', 'additional', 'fullUnload'
     */
    public static function removeHidden(&$typedLoads) {
        foreach($typedLoads as $typeKey => $loadsArray) {
            foreach($loadsArray as $loadKey => $loads) {
                foreach ($loads as $key => $load) {
                    if ($typeKey === 'direct') {
                        $loadArray = is_array($key) ? $key : [$key];

                        foreach(self::getHiddenSuggestions() as $hiddenArray) {
                            if ($loadArray === $hiddenArray) {
                                unset($typedLoads[$typeKey][$loadKey]);
                            }
                            continue;
                        }
                    } else {
                        $loadArray = is_array($load) ? $load : [$load];

                        foreach(self::getHiddenSuggestions() as $hiddenSuggestionsArray) {
                            foreach ($hiddenSuggestionsArray as $hiddenArray) {
                                $hiddenArray = is_array($hiddenArray) ? $hiddenArray : [$hiddenArray];
                                if ($loadArray === $hiddenArray) {
                                    unset($typedLoads[$typeKey][$loadKey]);
                                }
                            }
                            continue;
                        }
                    }
                }
            }
        }
    }

    /**
     * Returns each search results type count
     *
     * @param array $direct List of direct transportation loads IDs
     * @param array $additional List of additional transportation loads groups IDs
     * @param array $fullUnload List of full unload transportation loads groups IDs
     * @return static[]
     */
    public static function getLoadsTypesQuantity($direct = [], $additional = [], $fullUnload = [])
    {
        $typesCount = [
            'directLoadsTypeCount' => count($direct),
            'additionalLoadsTypeCount' => count($additional),
            'fullUnloadLoadsTypeCount' => count($fullUnload),
        ];

        return $typesCount;
    }

    /**
     * Returns each load suggestions type count
     *
     * @param array $day Day searches suggestions
     * @param array $previous Previous searches suggestions
     * @param array $signUpCity Sign up city searches suggestions
     * @return array
     */
    public static function getSuggestionsQuantity($day = [], $previous = [], $signUpCity = [])
    {
        $suggestionsCount = [
            'daySuggestionsCount' => [
                'direct' => count($day['direct']),
                'additional' => count($day['additional']),
                'fullUnload' => count($day['fullUnload']),
            ],
            'previousSuggestionsCount' => [
                'direct' => count($previous['direct']),
                'additional' => count($previous['additional']),
                'fullUnload' => count($previous['fullUnload']),
            ],
            'signUpCityCount' => [
                'direct' => count($signUpCity['direct']),
            ],
        ];

        return $suggestionsCount;
    }

    /**
     * Checks whether load date is invalid
     *
     * @return boolean
     */
    public function hasInvalidDate()
    {
        return $this->date === false || $this->date < 0 || $this->date === '0000-00-00';
    }

    /**
     * Checks whether load is visible to all users
     *
     * @param null|integer $id Load ID
     * @param integer $status Load status
     * @param integer $active Load activity status
     * @return boolean
     */
    public static function isLoadVisible($id, $status = self::ACTIVE, $active = self::ACTIVATED)
    {
        return self::find()->where(compact('id', 'status', 'active'))->exists();
    }

    /**
     * Returns list of translated loads types
     *
     * @return array
     */
    public static function getLoadsTypes()
    {
        return [
            self::TYPE_PARTIAL => Yii::t('element', 'L-T-3'),
            self::TYPE_FULL => Yii::t('element', 'L-T-4'),
        ];
    }

    /**
     * Returns loads active data provider
     *
     * @param ActiveQuery $query Loads query
     * @param integer $pageSize Page size selection value
     * @return ActiveDataProvider
     */
    public static function getLoadsDataProvider(ActiveQuery $query, $pageSize)
    {
        return new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => compact('pageSize'),
        ]);
    }

    /**
     * Converts load date, which is in timestamp format to data, that is in string format
     *
     * @return false|null|string
     */
    public function convertTimestampToDate()
    {
        return empty($this->date) ? null : date('Y-m-d', $this->date);
    }

    /**
     * Checks whether current load owner is specified user
     *
     * @param null|integer $userId Specific user ID
     * @return boolean
     */
    public function isOwner($userId)
    {
        return $this->user_id == $userId;
    }

    /**
     * @return array
     */
    public function getCarPosRanges()
    {
         $range = range(1, 10);
         return array_combine($range, $range);
    }

    /**
     * @return array
     */
    public function getDaysRanges()
    {
        $range = range(1, 7);
        return array_combine($range, $range);
    }

    /**
     * Checks if open contacts service exist by expiry date
     *
     * @return boolean
     */
    public function isOpenContacts()
    {
        return $this->open_contacts_expiry > time();
    }

    /**
     * Returns open contacts expiry date
     *
     * @return string
     */
    public function getOpenContactsExpiry()
    {
        return date($this->open_contacts_expiry, 'Y-m-d H:i');
    }

    /**
     * Returns open contacts expiry datetime
     *
     * @return string
     */
    public function getOpenContactsExpiryTime()
    {
        return date('Y-m-d H:i:s', $this->open_contacts_expiry);
    }

    /**
     * Sets load advertisement submit time
     */
    public function setAdvertisementSubmitTime()
    {
        $date = new DateTime();
        $this->submit_time_adv = $date->format('Y-m-d H:i:s');
    }

    /**
     * Sets open contacts days by open_contacts_days attribute value
     */
    public function setOpenContactsExpiry()
    {
        if (empty($this->open_contacts_days)) {
            return;
        }

        if ($this->open_contacts_expiry == 0) {
            $timestamp = time();
        } else {
            $timestamp = $this->open_contacts_expiry;
        }

        $days = $this->open_contacts_days;
        $this->open_contacts_expiry = strtotime("+{$days} days", $timestamp);
    }

    /**
     * @param int $type
     * @return string
     */
    public function getCityInfo($type = LoadCity::LOADING){
        $cities = [];
        $string = '';
        foreach ($this->loadCities as $lc) {
            if ($type === LoadCity::LOADING && $lc->isLoadingCity()) {
                $city = City::findOne($lc->city_id);
            } elseif ($type === LoadCity::UNLOADING && $lc->isUnloadingCity()) {
                $city = City::findOne($lc->city_id);
            } else {
                continue;
            }
            $cities[$city->country_code][$city->id] = $city->name;
        }
        foreach ($cities as $countryCode => $list) {
            $string .= $countryCode . ', ';
            foreach ($list as $cityName) {
                $string .= $cityName . ', ';
            }
            $string = rtrim($string, ', ') . ' + ';
        }
        return rtrim($string, ' + ');
    }

    /**
     * @param int $type
     * @return string
     */
    public function getFullCityInfo($type = LoadCity::LOADING){
        $cities = [];
        $string = '';
        $countries = City::getOriginalCountriesList();
        foreach ($this->loadCities as $lc) {
            if ($type === LoadCity::LOADING && $lc->isLoadingCity()) {
                $city = City::findOne($lc->city_id);
            } elseif ($type === LoadCity::UNLOADING && $lc->isUnloadingCity()) {
                $city = City::findOne($lc->city_id);
            } else {
                continue;
            }
            $cities[Yii::t('country', $countries[$city->country_code]['name'])][$city->id] = $city->name;
        }
        foreach ($cities as $countryCode => $list) {
            $string .= $countryCode . ', ';
            foreach ($list as $cityName) {
                $string .= $cityName . ', ';
            }
            $string = rtrim($string, ', ') . ' + ';
        }
        return rtrim($string, ' + ');
    }
}
