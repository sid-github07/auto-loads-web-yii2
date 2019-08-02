<?php

namespace common\models;

use borales\extensions\phoneInput\PhoneInputValidator;
use SoapClient;
use Yii;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "company".
 *
 * @property integer $id
 * @property integer $owner_id
 * @property string $title
 * @property string $code
 * @property string $vat_code
 * @property string $address
 * @property integer $city_id
 * @property string $phone
 * @property string $email
 * @property string $website
 * @property string $name
 * @property string $surname
 * @property string $personal_code
 * @property integer $active
 * @property integer $allow
 * @property integer $archive
 * @property integer $visible
 * @property integer $potential
 * @property integer $suggestions
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $ownerList
 * @property City $city
 * @property CompanyComment[] $companyComments
 * @property CompanyDocument[] $companyDocuments
 * @property CompanyInvitation[] $companyInvitations
 * @property CompanyUser[] $companyUsers
 * @property UserInvoice[] $userInvoices
 */
class Company extends ActiveRecord
{
    /** @const integer Maximum number of characters that allowed for title */
    const TITLE_MAX_LENGTH = 255;

    /** @const null Default title value */
    const TITLE_DEFAULT_VALUE = null;

    /** @const integer Maximum number of characters that allowed for code */
    const CODE_MAX_LENGTH = 255;

    /** @const null Default code value */
    const CODE_DEFAULT_VALUE = null;

    /** @const null Default VAT code value */
    const VAT_CODE_DEFAULT_VALUE = null;

    /** @const integer Maximum number of characters that allowed for address */
    const ADDRESS_MAX_LENGTH = 255;

    /** @const integer Maximum number of characters that allowed for email */
    const EMAIL_MAX_LENGTH = 255;

    /** @const null Default email value */
    const EMAIL_DEFAULT_VALUE = null;

    /** @const integer Maximum number of characters that allowed for website */
    const WEBSITE_MAX_VALUE = 255;

    /** @const null Default website value */
    const WEBSITE_DEFAULT_VALUE = null;

    /** @const integer Maximum number of characters that allowed for name */
    const NAME_MAX_LENGTH = 255;

    /** @const null Default name value */
    const NAME_DEFAULT_VALUE = null;

    /** @const integer Maximum number of characters that allowed for surname */
    const SURNAME_MAX_LENGTH = 255;

    /** @const null Default surname value */
    const SURNAME_DEFAULT_VALUE = null;

    /** @const null Default value for personal code */
    const PERSONAL_CODE_DEFAULT_VALUE = null;

    /** @const boolean Company is inactive */
    const INACTIVE = 0;

    /** @const boolean Company is active */
    const ACTIVE = 1;

    /** @const boolean Company is forbidden to make actions */
    const FORBIDDEN = 0;

    /** @const boolean Company is allowed to make actions */
    const ALLOW = 1;

    /** @const boolean Company is not archived */
    const NOT_ARCHIVED = 0;

    /** @const boolean Company is archived */
    const ARCHIVED = 1;

    /** @const boolean Company is invisible to others */
    const INVISIBLE = 0;

    /** @const boolean Company is visible to others */
    const VISIBLE = 1;
    
    /** @const boolean Company is not potential */
    const NOT_POTENTIAL = 0;

    /** @const boolean Company is potential */
    const POTENTIAL = 1;

    /** @const boolean Company users do not want to get load suggestions */
    const DO_NOT_SEND_SUGGESTIONS = 0;

    /** @const boolean Company users want to get load suggestions */
    const SEND_SUGGESTIONS = 1;
    
    /** @const integer Company has invited users  */
    const HAS_INVITED_USERS = 1;
    
    /** @const integer Company has not invited users  */
    const HAS_NOT_INVITED_USERS = 0;
    
    /** @const string Company active type extended search */
    const IS_ARCHIVED = 'archived';
    
    /** @const string Company active type extended search */
    const IS_ACTIVE = 'active';
    
    /** @const integer Minimum number of characters that VAT code MUST have */
    const VAT_CODE_MIN_LENGTH = 2;

    /** @const integer Maximum number of characters that change VAT code attribute can have */
    const CHANGE_VAT_CODE_MAX_LENGTH = 3000;
    
    /** @const string Model scenario when creating new natural company */
    const SCENARIO_CREATE_NATURAL = 'create-natural';

    /** @const string Model scenario when creating new legal company */
    const SCENARIO_CREATE_LEGAL = 'create-legal';

    /** @const string Model scenario when editing contact info while user account type is natural */
    const SCENARIO_CONTACT_INFO_NATURAL = 'contact-info-natural';

    /** @const string Model scenario when editing contact info while user account type is legal */
    const SCENARIO_CONTACT_INFO_LEGAL = 'contact-info-legal';

    /** @const string Model scenario when company owner wants to change VAT code */
    const SCENARIO_CHANGE_VAT_CODE = 'change-vat-code';
    
    /** @const string Model scenario when administrator uses extended search filter to find clients  */
    const SCENARIO_EXTENDED_CLIENT_SEARCH = 'extended-client-search';
    
    /** @const string Model scenario when changing company status  */
    const SCENARIO_CHANGE_COMPANY_STATUS_SERVER = 'change-company-status-server';
    
    /** @const string Model scenario when editing company data client side */
    const SCENARIO_EDIT_COMPANY_DATA_CLIENT = 'edit-company-data-client';

    /** @const string Model scenario when administrator filters planned income */
    const SCENARIO_ADMIN_FILTERS_PLANNED_INCOMES = 'admin-filters-planned-income';

    /** @const string Model scenario when administrator changes company title */
    const SCENARIO_ADMIN_CHANGES_COMPANY_TITLE = 'admin-changes-company-title';
    
    /** @const string Model scenario when admin changes company potentiality */
    const SCENARIO_CHANGE_COMPANY_POTENTIALITY = 'change-company-potentiality';

    /** @const string Model scenario when administrator changes company name and surname */
    const SCENARIO_ADMIN_CHANGES_COMPANY_NAME_SURNAME = 'admin-changes-company-name-surname';

    /** @const string Model scenario when system migrates company data from one database to another */
    const SCENARIO_SYSTEM_MIGRATES_COMPANY_DATA = 'system-migrates-company-data';


    const SCENARIO_SYSTEM_MIGRATES_COMPANY = 'system-migrates-company';

    /** @const string Model scenario when a guest is buying creditcodes */
    const SCENARIO_GUEST_BUYS_CREDITCODE_LEGAL = 'guest-buys-credit-code-legal';
    const SCENARIO_GUEST_BUYS_CREDITCODE_NATURAL = 'guest-buys-credit-code-natural';

    /** @const string Search input name in company index */
    const COMPANY_FILTER_INPUT_NAME = 'company-search-request';
    
    /** @var null|User Company owner (User) model */
    private $owner = null;

    /** @var string Company owner text when wants to change VAT code */
    public $changeVatCode;

    /** @var array Company documents */
    private $documents = [];
    
    /** @var integer Company has invited users */
    public $invitedUsers;
    
    /** @var integer Company status */
    public $companyStatus;

    /**
     * @inheritdoc
     */
    public function __construct($ownerId = null, $scenario = null, $config = [])
    {
        if (!is_null($ownerId)) {
            $this->setOwner($ownerId);
        }
        if (!is_null($scenario)) {
            $this->setManualScenario($scenario);
        }
        parent::__construct($config);
    }

    /**
     * Sets company owner
     *
     * @param null|integer $userId Company owner ID
     */
    public function setOwner($userId = null)
    {
        $this->owner = User::findOne(['id' => $userId]);
    }
    
    /**
     * Sets scenario for company
     *
     * @param string $scenario company scenario
     */
    public function setManualScenario($scenario)
    {
        $this->scenario = $scenario;
    }

    /**
     * Returns company owner
     *
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Sets company documents
     *
     * @param array $documents Company documents
     */
    public function setDocuments($documents = [])
    {
        $this->documents = $documents;
    }

    /**
     * Returns company documents
     *
     * @return array
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE_NATURAL => [
                'owner_id',
                'address',
                'city_id',
                'phone',
                'email',
                'name',
                'surname',
                'personal_code',
                'active',
                'allow',
                'archive',
                'visible',
                'potential',
                'created_at',
                'updated_at',
            ],
            self::SCENARIO_CREATE_LEGAL => [
                'owner_id',
                'title',
                'code',
                'vat_code',
                'address',
                'city_id',
                'phone',
                'email',
                'website',
                'active',
                'allow',
                'archive',
                'visible',
                'potential',
                'created_at',
                'updated_at',
            ],
            self::SCENARIO_CONTACT_INFO_NATURAL => [
                'personal_code',
                'name',
                'surname',
                'address',
                'city_id',
                'phone',
                'email',
            ],
            self::SCENARIO_CONTACT_INFO_LEGAL => [
                'code',
                'title',
                'address',
                'city_id',
                'phone',
                'email',
                'website',
            ],
            self::SCENARIO_CHANGE_VAT_CODE => [
                'changeVatCode',
            ],
            self::SCENARIO_EXTENDED_CLIENT_SEARCH => [
                'companyStatus',
                'invitedUsers',
                'city_id',
                'potential',
            ],
            self::SCENARIO_CHANGE_COMPANY_STATUS_SERVER => [
                'archive',
                'active',
            ],
            self::SCENARIO_EDIT_COMPANY_DATA_CLIENT => [
                'vat_code',
                'code',
                'address',
                'city_id',
                'phone',
                'email',
                'owner_id',
                'active',
                'visible',
                'website',
                'suggestions',
            ],
            self::SCENARIO_ADMIN_FILTERS_PLANNED_INCOMES => [
                'title',
            ],
            self::SCENARIO_ADMIN_CHANGES_COMPANY_TITLE => [
                'title',
            ],
            self::SCENARIO_ADMIN_CHANGES_COMPANY_NAME_SURNAME => [
                'name',
                'surname',
            ],
            self::SCENARIO_SYSTEM_MIGRATES_COMPANY_DATA => [
                'id',
                'owner_id',
                'title',
                'code',
                'vat_code',
                'address',
                'city_id',
                'phone',
                'email',
                'website',
                'name',
                'surname',
                'personal_code',
                'active',
                'allow',
                'archive',
                'visible',
                'potential',
                'suggestions',
                'created_at',
                'updated_at',
            ],
            self::SCENARIO_SYSTEM_MIGRATES_COMPANY => [
                'id',
                'owner_id',
                'title',
                'code',
                'vat_code',
                'address',
                'city_id',
                'phone',
                'email',
                'website',
                'name',
                'surname',
                'personal_code',
                'active',
                'allow',
                'archive',
                'visible',
                'potential',
                'suggestions',
                'created_at',
                'updated_at',
            ],
            self::SCENARIO_CHANGE_COMPANY_POTENTIALITY => [
                'potential'
            ],
            self::SCENARIO_GUEST_BUYS_CREDITCODE_LEGAL => [
                'id',
                'owner_id',
                'title',
                'code',
                'vat_code',
                'address',
                'city_id',
                'email',
                'name',
                'surname',
                'personal_code',
                'active',
                'allow',
                'archive',
                'visible',
                'potential',
                'created_at',
                'updated_at',
            ],
            self::SCENARIO_GUEST_BUYS_CREDITCODE_NATURAL => [
                'id',
                'owner_id',
                'address',
                'city_id',
                'email',
                'name',
                'surname',
                'personal_code',
                'active',
                'allow',
                'archive',
                'visible',
                'potential',
                'created_at',
                'updated_at',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%company}}';
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
    public function rules()
    {
        return [
            // Owner ID
            ['owner_id', 'required', 'message' => Yii::t('app', 'COMPANY_OWNER_ID_IS_REQUIRED')],
            ['owner_id', 'integer', 'message' => Yii::t('app', 'COMPANY_OWNER_ID_IS_INTEGER')],
            ['owner_id', 'exist', 'targetClass' => User::className(),
                              'targetAttribute' => ['owner_id' => 'id'],
                                      'message' => Yii::t('app', 'COMPANY_OWNER_ID_NOT_EXIST')],

            // Title
            ['title', 'required', 'message' => Yii::t('app', 'COMPANY_TITLE_IS_REQUIRED'),
                                   'except' => [
                                       self::SCENARIO_ADMIN_FILTERS_PLANNED_INCOMES,
                                       self::SCENARIO_SYSTEM_MIGRATES_COMPANY_DATA,
                                       self::SCENARIO_SYSTEM_MIGRATES_COMPANY,
                                       self::SCENARIO_GUEST_BUYS_CREDITCODE_NATURAL,
                                   ]],
            ['title', 'string', 'max' => self::TITLE_MAX_LENGTH,
                            'tooLong' => Yii::t('app', 'COMPANY_TITLE_IS_TOO_LONG', [
                                'length' => self::TITLE_MAX_LENGTH,
                            ])],
            ['title', 'unique', 'targetClass' => '\common\models\Company',
                                    'message' => Yii::t('app', 'COMPANY_TITLE_IS_NOT_UNIQUE', [
                                        'userEmail' => User::getEmailByTitle($this->title),
                                        'adminEmail' => Yii::$app->params['adminEmail']
                                    ]),
                                     'except' => [
                                         self::SCENARIO_ADMIN_FILTERS_PLANNED_INCOMES,
                                         self::SCENARIO_SYSTEM_MIGRATES_COMPANY_DATA,
                                         self::SCENARIO_SYSTEM_MIGRATES_COMPANY,
                                         self::SCENARIO_GUEST_BUYS_CREDITCODE_NATURAL,
                                         self::SCENARIO_GUEST_BUYS_CREDITCODE_LEGAL,
                                     ]],
            ['title', 'default', 'value' => self::TITLE_DEFAULT_VALUE],
            ['title', 'filter', 'filter' => 'trim',
                                'except' => [
                                    self::SCENARIO_SYSTEM_MIGRATES_COMPANY_DATA,
                                    self::SCENARIO_SYSTEM_MIGRATES_COMPANY,
                                ]],

            // Code
            ['code', 'required', 'message' => Yii::t('app', 'COMPANY_CODE_IS_REQUIRED'),
                            'except' => [
                                self::SCENARIO_EDIT_COMPANY_DATA_CLIENT,
                                self::SCENARIO_SYSTEM_MIGRATES_COMPANY_DATA,
                                self::SCENARIO_SYSTEM_MIGRATES_COMPANY,
                                self::SCENARIO_GUEST_BUYS_CREDITCODE_NATURAL,
                            ]],
            ['code', 'string', 'max' => self::CODE_MAX_LENGTH,
                           'tooLong' => Yii::t('app', 'COMPANY_CODE_IS_TOO_LONG', [
                               'length' => self::CODE_MAX_LENGTH,
                           ])],
            ['code', 'unique', 'targetClass' => '\common\models\Company',
                                   'message' => Yii::t('app', 'COMPANY_CODE_IS_NOT_UNIQUE'),
                                    'except' => [
                                        self::SCENARIO_SYSTEM_MIGRATES_COMPANY_DATA,
                                        self::SCENARIO_SYSTEM_MIGRATES_COMPANY,
                                        self::SCENARIO_GUEST_BUYS_CREDITCODE_NATURAL,
                                        self::SCENARIO_GUEST_BUYS_CREDITCODE_LEGAL,
                                    ]],
            ['code', 'default', 'value' => self::CODE_DEFAULT_VALUE],
            ['code', 'filter', 'filter' => 'trim',
                               'except' => [
                                   self::SCENARIO_SYSTEM_MIGRATES_COMPANY_DATA,
                                   self::SCENARIO_SYSTEM_MIGRATES_COMPANY,
                               ]],

            // VAT code
            ['vat_code', 'validateVatCode', 'except' => self::SCENARIO_SYSTEM_MIGRATES_COMPANY],
            ['vat_code', 'default', 'value' => self::VAT_CODE_DEFAULT_VALUE],

            // Address
            ['address', 'required', 'message' => Yii::t('app', 'COMPANY_ADDRESS_IS_REQUIRED')],
            ['address', 'string', 'max' => self::ADDRESS_MAX_LENGTH,
                              'tooLong' => Yii::t('app', 'COMPANY_ADDRESS_IS_TOO_LONG', [
                                  'length' => self::ADDRESS_MAX_LENGTH,
                              ])],
            ['address', 'filter', 'filter' => 'trim'],

            // City ID
            ['city_id', 'required', 'message' => Yii::t('app', 'COMPANY_CITY_ID_IS_REQUIRED'),
                'except' => [self::SCENARIO_EXTENDED_CLIENT_SEARCH]],
            ['city_id', 'integer', 'message' => Yii::t('app', 'COMPANY_CITY_ID_IS_INTEGER'),
                'except' => self::SCENARIO_EXTENDED_CLIENT_SEARCH],
            ['city_id', 'exist', 'targetClass' => City::className(),
                             'targetAttribute' => ['city_id' => 'id'],
                                     'message' => Yii::t('app', 'COMPANY_CITY_ID_NOT_EXIST'),
                'except' => self::SCENARIO_EXTENDED_CLIENT_SEARCH],

            // Phone
            ['phone', 'required', 'message' => Yii::t('app', 'COMPANY_PHONE_IS_REQUIRED'),
                                'except' => [
                                    self::SCENARIO_GUEST_BUYS_CREDITCODE_LEGAL,
                                    self::SCENARIO_GUEST_BUYS_CREDITCODE_NATURAL
                                ]],
            ['phone', PhoneInputValidator::className(),
                'message' => Yii::t('app', 'COMPANY_PHONE_IS_NOT_MATCH'),
                'except' => self::SCENARIO_SYSTEM_MIGRATES_COMPANY],

            // Email
            ['email', 'required', 'message' => Yii::t('app', 'COMPANY_EMAIL_IS_REQUIRED'),
                'except' => [
                                self::SCENARIO_SYSTEM_MIGRATES_COMPANY,
                                self::SCENARIO_GUEST_BUYS_CREDITCODE_LEGAL,
                                self::SCENARIO_GUEST_BUYS_CREDITCODE_NATURAL
                ]],
            ['email', 'email', 'message' => Yii::t('app', 'COMPANY_EMAIL_IS_NOT_EMAIL'),
                'except' => self::SCENARIO_SYSTEM_MIGRATES_COMPANY],
            ['email', 'string', 'max' => self::EMAIL_MAX_LENGTH,
                            'tooLong' => Yii::t('app', 'COMPANY_EMAIL_IS_TOO_LONG', [
                                'length' => self::EMAIL_MAX_LENGTH,
                            ])],
            ['email', 'default', 'value' => self::EMAIL_DEFAULT_VALUE],
            ['email', 'filter', 'filter' => function ($value) {
                                return is_null($value) ? $value : trim($value);
                            },
                'except' => self::SCENARIO_SYSTEM_MIGRATES_COMPANY],
            ['email', 'unique', 'targetClass' => '\common\models\Company',
                                   'message' => Yii::t('app', 'COMPANY_EMAIL_IS_NOT_UNIQUE'),
                                    'except' => self::SCENARIO_SYSTEM_MIGRATES_COMPANY],

            // Website
            ['website', 'string', 'max' => self::WEBSITE_MAX_VALUE,
                              'tooLong' => Yii::t('app', 'COMPANY_WEBSITE_IS_TOO_LONG', [
                                  'length' => self::WEBSITE_MAX_VALUE,
                              ])],
            ['website', 'url', 'defaultScheme' => 'http',
                                'validSchemes' => ['http', 'https'],
                                     'message' => Yii::t('app', 'COMPANY_WEBSITE_IS_NOT_URL')],
            ['website', 'default', 'value' => self::WEBSITE_DEFAULT_VALUE],
            ['website', 'filter', 'filter' => function ($value) {
                                    return is_null($value) ? $value : trim($value);
                                }],

            // Name
            ['name', 'required', 'message' => Yii::t('app', 'COMPANY_NAME_IS_REQUIRED'),
                                  'except' => [
                                      self::SCENARIO_SYSTEM_MIGRATES_COMPANY_DATA,
                                      self::SCENARIO_SYSTEM_MIGRATES_COMPANY,
                                      self::SCENARIO_GUEST_BUYS_CREDITCODE_LEGAL
                                  ]],
            ['name', 'string', 'max' => self::NAME_MAX_LENGTH,
                           'tooLong' => Yii::t('app', 'COMPANY_NAME_IS_TOO_LONG', [
                               'length' => self::NAME_MAX_LENGTH,
                           ])],
            ['name', 'validateName', 'params' => [
                'message' => Yii::t('app', 'COMPANY_NAME_IS_NOT_MATCH'),
            ]],
            ['name', 'default', 'value' => self::NAME_DEFAULT_VALUE],
            ['name', 'filter', 'filter' => 'trim'],

            // Surname
            ['surname', 'required', 'message' => Yii::t('app', 'COMPANY_SURNAME_IS_REQUIRED'),
                                     'except' => [
                                         self::SCENARIO_SYSTEM_MIGRATES_COMPANY_DATA,
                                         self::SCENARIO_SYSTEM_MIGRATES_COMPANY,
                                         self::SCENARIO_GUEST_BUYS_CREDITCODE_LEGAL
                                     ]],
            ['surname', 'string', 'max' => self::SURNAME_MAX_LENGTH,
                              'tooLong' => Yii::t('app', 'COMPANY_SURNAME_IS_TOO_LONG', [
                                  'length' => self::SURNAME_MAX_LENGTH,
                              ])],
            ['surname', 'validateName', 'params' => [
                'message' => Yii::t('app', 'COMPANY_SURNAME_IS_NOT_MATCH'),
            ]],
            ['surname', 'default', 'value' => self::SURNAME_DEFAULT_VALUE],
            ['surname', 'filter', 'filter' => 'trim'],

            // Personal code
            ['personal_code', 'unique', 'targetClass' => '\common\models\Company',
                                            'message' => Yii::t('app', 'COMPANY_PERSONAL_CODE_IS_NOT_UNIQUE'),
                                             'except' => [
                                                 self::SCENARIO_SYSTEM_MIGRATES_COMPANY_DATA,
                                                 self::SCENARIO_SYSTEM_MIGRATES_COMPANY,
                                             ]],
            ['personal_code', 'default', 'value' => self::PERSONAL_CODE_DEFAULT_VALUE],
            ['personal_code', 'match', 'pattern' => '/^[\-\.\/\s\w]{5,18}$/',
                                       'message' => Yii::t('app', 'COMPANY_PERSONAL_CODE_IS_NOT_MATCH')],

            // Active
            ['active', 'required', 'message' => Yii::t('app', 'COMPANY_ACTIVE_IS_REQUIRED')],
            ['active', 'in', 'range' => [self::INACTIVE, self::ACTIVE],
                           'message' => Yii::t('app', 'COMPANY_ACTIVE_IS_NOT_IN_RANGE'),
                           'except' => self::SCENARIO_EDIT_COMPANY_DATA_CLIENT],

            // Allow
            ['allow', 'required', 'message' => Yii::t('app', 'COMPANY_ALLOW_IS_REQUIRED')],
            ['allow', 'in', 'range' => [self::FORBIDDEN, self::ALLOW],
                          'message' => Yii::t('app', 'COMPANY_ALLOW_IS_NOT_IN_RANGE')],

            // Archive
            ['archive', 'required', 'message' => Yii::t('app', 'COMPANY_ARCHIVE_IS_REQUIRED')],
            ['archive', 'in', 'range' => [self::NOT_ARCHIVED, self::ARCHIVED],
                            'message' => Yii::t('app', 'COMPANY_ARCHIVE_IS_NOT_IN_RANGE')],

            // Visible
            ['visible', 'required', 'message' => Yii::t('app', 'COMPANY_VISIBLE_IS_REQUIRED')],
            ['visible', 'in', 'range' => [self::INACTIVE, self::VISIBLE],
                            'message' => Yii::t('app', 'COMPANY_VISIBLE_IS_NOT_IN_RANGE')],
                                        
            // Potential
            ['potential', 'required', 'message' => Yii::t('app', 'COMPANY_POTENTIAL_IS_REQUIRED'),
                'except' => self::SCENARIO_EXTENDED_CLIENT_SEARCH],
            ['potential', 'default', 'value' => self::NOT_POTENTIAL,
                'except' => self::SCENARIO_EXTENDED_CLIENT_SEARCH],                            
            ['potential', 'in', 'range' => [self::NOT_POTENTIAL, self::POTENTIAL],
                            'message' => Yii::t('app', 'COMPANY_POTENTIAL_IS_NOT_IN_RANGE')],                            

            // Suggestions
            ['suggestions', 'required', 'message' => Yii::t('app', 'COMPANY_SUGGESTIONS_IS_REQUIRED')],
            ['suggestions', 'default', 'value' => self::SEND_SUGGESTIONS],
            ['suggestions', 'in', 'range' => [self::DO_NOT_SEND_SUGGESTIONS, self::SEND_SUGGESTIONS],
                                'message' => Yii::t('app', 'COMPANY_SUGGESTIONS_IS_NOT_IN_RANGE')],

            // Created at
            ['created_at', 'integer', 'message' => Yii::t('app', 'COMPANY_CREATED_AT_IS_NOT_INTEGER')],

            // Updated at
            ['updated_at', 'integer', 'message' => Yii::t('app', 'COMPANY_UPDATED_AT_IS_NOT_INTEGER')],

            // Change VAT code
            ['changeVatCode', 'required', 'message' => Yii::t('app', 'COMPANY_CHANGE_VAT_CODE_IS_REQUIRED')],
            ['changeVatCode', 'filter', 'filter' => 'trim'],
            ['changeVatCode', 'string', 'max' => self::CHANGE_VAT_CODE_MAX_LENGTH,
                                    'tooLong' => Yii::t('app', 'COMPANY_CHANGE_VAT_CODE_IS_TOO_LONG', [
                                        'length' => self::CHANGE_VAT_CODE_MAX_LENGTH,
                                    ])],
            
            // Invited Users
            ['invitedUsers', 'in', 'range' => [self::HAS_INVITED_USERS, self::HAS_NOT_INVITED_USERS],
                           'message' => Yii::t('app', 'COMPANY_INVITED_USER_IS_NOT_IN_RANGE'),
                           'on' => self::SCENARIO_EXTENDED_CLIENT_SEARCH
                           ],
                                        
            // Company Status
            ['companyStatus', 'in', 'range' => [self::IS_ACTIVE, self::IS_ARCHIVED],
                           'message' => Yii::t('app', 'COMPANY_INVITED_USER_IS_NOT_IN_RANGE'),
                           'on' => self::SCENARIO_EXTENDED_CLIENT_SEARCH
                           ],                               
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'owner_id' => Yii::t('app', 'COMPANY_OWNER_ID_LABEL'),
            'title' => Yii::t('app', 'COMPANY_TITLE_LABEL'),
            'code' => Yii::t('app', 'COMPANY_CODE_LABEL'),
            'vat_code' => Yii::t('app', 'COMPANY_VAT_CODE_LABEL'),
            'address' => Yii::t('app', 'COMPANY_ADDRESS_LABEL'),
            'city_id' => Yii::t('app', 'COMPANY_CITY_ID_LABEL'),
            'phone' => Yii::t('app', 'COMPANY_PHONE_LABEL'),
            'email' => Yii::t('app', 'COMPANY_EMAIL_LABEL'),
            'website' => Yii::t('app', 'COMPANY_WEBSITE_LABEL'),
            'name' => Yii::t('app', 'COMPANY_NAME_LABEL'),
            'surname' => Yii::t('app', 'COMPANY_SURNAME_LABEL'),
            'personal_code' => Yii::t('app', 'COMPANY_PERSONAL_CODE_LABEL'),
            'active' => Yii::t('app', 'COMPANY_ACTIVE_LABEL'),
            'allow' => Yii::t('app', 'COMPANY_ALLOW_LABEL'),
            'archive' => Yii::t('app', 'COMPANY_ARCHIVE_LABEL'),
            'visible' => Yii::t('app', 'COMPANY_VISIBLE_LABEL'),
            'potential' => Yii::t('app', 'COMPANY_POTENTIAL_LABEL'),
            'suggestions' => Yii::t('app', 'COMPANY_SUGGESTIONS_LABEL'),
            'created_at' => Yii::t('app', 'COMPANY_CREATED_AT_LABEL'),
            'updated_at' => Yii::t('app', 'COMPANY_UPDATED_AT_LABEL'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCompanyDocuments()
    {
        return $this->hasMany(CompanyDocument::className(), ['company_id' => 'id']);
    }
    
    /**
     * @return ActiveQuery
     */
    public function getOwnerList()
    {
        return $this->hasOne(User::className(), ['id' => 'owner_id'])
                ->from(User::tableName() . ' ownerList');
    }
    
    /**
     * @return ActiveQuery
     */
    public function getCompanyUsers()
    {
        return $this->hasMany(CompanyUser::className(), ['company_id' => 'id']);
    }
    
    /**
     * @return ActiveQuery
     */
    public function getCompanyDocument()
    {
        return $this->hasMany(CompanyDocument::className(), ['company_id' => 'id']);
    }

    /**
     * Validates VAT code
     *
     * @param string $attribute Attribute name that is being validated
     */
    public function validateVatCode($attribute)
    {
        if (!User::isVatCodeLengthValid($this->$attribute)) {
            return;
        }
        if (!preg_match('/^[A-Z]{2}[0-9A-Z]{2,15}$/', $this->$attribute)) {
            $this->addError($attribute, Yii::t('app', 'VALIDATE_VAT_CODE_NUMBER_IS_NOT_MATCH'));
        }
        list($code, $number) = User::splitVatCode($this->$attribute, User::VAT_CODE_MIN_LENGTH);
        $countries = Country::getVatRateCountries();
        if (!array_key_exists($code, $countries)) {
            $this->addError($this->$attribute, Yii::t('app', 'VALIDATE_VAT_CODE_INVALID_COUNTRY_CODE'));
        }
        if (!User::isVatCodeValidByEC($code, $number)) {
            $this->addError($attribute, Yii::t('app', 'VALIDATE_VAT_CODE_NOT_IN_EUROPEAN_COMMISSION'));
        }
    }

    /**
     * Validates name or surname
     *
     * @param string $attribute Attribute name that is being validated
     * @param array $params The value of the "params" given in the rule
     */
    public function validateName($attribute, $params)
    {
        $name = trim($this->$attribute);
        if (!preg_match('/^((\b[a-zA-Z\p{L}\-\.]{1,}\b)\s*){1,}$/u', $name)) {
            $this->addError($attribute, $params['message']);
        }
    }

    /**
     * Loads company model by company owner or company user
     *
     * @param null|integer $userId User ID
     * @return null|static
     * @throws NotFoundHttpException If current logged in user is not neither company owner nor company user
     */
    public static function getCompany($userId = null)
    {
        $company = self::findByOwner($userId);
        if (is_null($company)) {
            $company = self::findByUser($userId);
            if (is_null($company)) {
                $company = self::findByCompanyId($userId);
                if (is_null($company)) {
                    throw new NotFoundHttpException(Yii::t('alert', 'COMPANY_NOT_FOUND_BY_USER'));
                }
            }
        }

        if (is_null($company->getOwner())) {
            $company->setOwner($company->owner_id);
        }
        $companyDocuments = CompanyDocument::findByCompany($company->id);
        $company->setDocuments($companyDocuments);
        return $company;
    }

    /**
     * Finds company by company owner
     *
     * @param null|integer $ownerId Company owner ID
     * @return null|static
     */
    public static function findByOwner($ownerId = null)
    {
        if (is_null($ownerId)) {
            $ownerId = Yii::$app->getUser()->getId();
        }
        return self::findOne(['owner_id' => $ownerId]);
    }
    
    /**
     * Finds company by company id
     *
     * @param null|integer $ownerId Company owner ID
     * @return null|static
     */
    public static function findByCompanyId($companyId = null)
    {
        if (!is_null($companyId)) {
            return self::findOne(['id' => $companyId]);
        }
    }

    /**
     * Finds company by provided company user ID
     *
     * @param null|integer $userId Company user ID
     * @return self|null|array|ActiveRecord
     */
    public static function findByUser($userId = null)
    {
        if (is_null($userId)) {
            $userId = Yii::$app->getUser()->getId();
        }

        return self::find()
            ->innerJoin(CompanyUser::tableName(), CompanyUser::tableName() . '.company_id = ' . self::tableName() . '.id')
            ->where(CompanyUser::tableName() . '.user_id = ' . $userId)
            ->one();
    }

    /**
     * Creates new company
     *
     * @return boolean Whether company was created successfully
     * @throws NotAcceptableHttpException If user account type is invalid
     */
    public function create()
    {
        /** @var User $user */
        $user = $this->getOwner();
        $this->setAttribute('owner_id', $user->getAttribute('id'));
        $this->setAttribute('address', $user->getAttribute('address'));
        $this->setAttribute('city_id', $user->getAttribute('city_id'));
        $this->setAttribute('phone', $user->getAttribute('phone'));
        $this->setAttribute('email', $user->getAttribute('email'));
        $this->makeActive();
        $this->makeAllow();
        $this->makeNotArchived();
        $this->makeVisible();
        $this->potential = self::NOT_POTENTIAL;
        switch ($user->getAttribute('account_type')) {
            case User::NATURAL:
                $this->setScenario(self::SCENARIO_CREATE_NATURAL);
                $this->setAttribute('name', $user->getAttribute('name'));
                $this->setAttribute('surname', $user->getAttribute('surname'));
                $this->setAttribute('personal_code', self::PERSONAL_CODE_DEFAULT_VALUE);
                break;
            case User::LEGAL:
                $this->setScenario(self::SCENARIO_CREATE_LEGAL);
                $this->setAttribute('title', $user->getAttribute('company_name'));
                $this->setAttribute('code', $user->getAttribute('company_code'));
                $this->setAttribute('vat_code', $user->getAttribute('vat_code'));
                break;
            default:
                throw new NotAcceptableHttpException(Yii::t('alert', 'INVALID_USER_ACCOUNT_TYPE'));
        }
        $this->validate();
        return $this->save();
    }

    /**
     * Creates new company for creditcode buyers for invoice creation
     *
     * @return boolean Whether company was created successfully
     * @throws NotAcceptableHttpException If user account type is invalid
     */
    public function createCreditCodeCompany()
    {
        /** @var User $user */
        $user = $this->getOwner();
        $this->setAttribute('owner_id', $user->getAttribute('id'));
        $this->setAttribute('address', $user->getAttribute('address'));
        $this->setAttribute('city_id', $user->getAttribute('city_id'));
        $this->active = self::INACTIVE;
        $this->allow = self::FORBIDDEN;
        $this->archive = self::ARCHIVED;
        $this->visible = self::INVISIBLE;
        $this->potential = self::NOT_POTENTIAL;
        switch ($user->getAttribute('account_type')) {
            case User::NATURAL:
                $this->setScenario(self::SCENARIO_GUEST_BUYS_CREDITCODE_NATURAL);
                $this->setAttribute('name', $user->getAttribute('name'));
                $this->setAttribute('surname', $user->getAttribute('surname'));
                $this->setAttribute('personal_code', self::PERSONAL_CODE_DEFAULT_VALUE);
                break;
            case User::LEGAL:
                $this->setScenario(self::SCENARIO_GUEST_BUYS_CREDITCODE_LEGAL);
                $this->setAttribute('title', $user->getAttribute('company_name'));
                $this->setAttribute('code', $user->getAttribute('company_code'));
                $this->setAttribute('vat_code', $user->getAttribute('vat_code'));
                break;
            default:
                throw new NotAcceptableHttpException(Yii::t('alert', 'INVALID_USER_ACCOUNT_TYPE'));
        }
        $this->validate();
        return $this->save();
    }

    /**
     * Makes company active
     */
    public function makeActive()
    {
        $this->active = self::ACTIVE;
    }

    /**
     * Makes company allow to make actions
     */
    public function makeAllow()
    {
        $this->allow = self::ALLOW;
    }

    /**
     * Makes company not archived
     */
    public function makeNotArchived()
    {
        $this->archive = self::NOT_ARCHIVED;
    }

    /**
     * Makes company visible
     */
    public function makeVisible()
    {
        $this->visible = self::VISIBLE;
    }

    /**
     * Requests administrator for company VAT code change
     *
     * @return bool Whether mail was sent successfully
     * @throws NotAcceptableHttpException If email content is invalid
     */
    public function requestVatCodeChange()
    {
        if (!$this->validate()) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'REQUEST_VAT_CODE_CHANGE_NOT_VALID'));
        }
        return Yii::$app->mailer->compose('company/vat-code-change-request', [
                                'content' => $this->changeVatCode,
                                'userEmail' => Yii::$app->user->identity->email,
                            ])
                                ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->params['companyName']])
                                ->setTo(Yii::$app->params['adminEmail'])
                                ->setSubject(Yii::t('mail', 'COMPANY_REQUEST_VAT_CODE_CHANGE_SUBJECT'))
                                ->send();
    }

    /**
     * Returns contacts info scenario by company owners account type
     *
     * @return null|string
     */
    public function getScenarioByOwnersAccountType()
    {
        switch ($this->getOwner()->account_type) {
            case User::NATURAL;
                return Company::SCENARIO_CONTACT_INFO_NATURAL;
            case User::LEGAL;
                return Company::SCENARIO_CONTACT_INFO_LEGAL;
            default:
                return null;
        }
    }

    /**
     * Checks whether provided user is current company ID
     *
     * @param null|integer $ownerId Company owner ID
     * @return boolean
     */
    public function isOwner($ownerId = null)
    {
        if (is_null($ownerId)) {
            $ownerId = Yii::$app->getUser()->getId();
        }

        return $this->getOwner()->id === $ownerId;
    }

    /**
     * Checks whether company type is natural
     *
     * @return boolean
     */
    public function isNatural()
    {
        return empty($this->title) || is_null($this->title);
    }

    /**
     * Returns company title by company type
     *
     * @return string
     */
    public function getTitleByType()
    {
        return $this->isNatural() ? $this->getNameAndSurname() : $this->getTitle();
    }

    /**
     * Returns company owner name and surname
     *
     * @return string
     */
    public function getNameAndSurname()
    {
        return $this->name . ' ' . $this->surname;
    }

    /**
     * Returns company title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns company code by company type
     *
     * @return string
     */
    public function getCodeByType()
    {
        return $this->isNatural() ? $this->getPersonalCode() : $this->getCode();
    }

    /**
     * Returns company owner personal code
     *
     * @return string
     */
    public function getPersonalCode()
    {
        return $this->personal_code;
    }

    /**
     * Returns company code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
    
    /** 
     * Gets all companies and companies owner list
     * 
     * @return Company[]
     */
    public static function getAllCompaniesForClientIndex() 
    {
        $query = self::find()
                ->joinWith('ownerList')
                ->joinWith('ownerList.userServiceActive')
				->joinWith('ownerList.cameFrom')
                ->joinWith('companyUsers')
                ->joinWith('companyUsers.user as companyUser')
                ->joinWith('companyUsers.user.userServiceActive as companyUserService')
                ->orderBy(['company.created_at' => SORT_DESC]);
        return $query;
    }
    
    /** 
     * Gets all filtered companies
     * 
     * @return Company[]
     */
    public static function getAllFilteredCompaniesForClientIndex($post = null)
    {
        $query = self::getCompaniesQuery();
        
        $query = self::textSearchByCompanyName($query, $post[Company::COMPANY_FILTER_INPUT_NAME]);
        
        $query = self::textSearchByCompanyUsers($query, $post[Company::COMPANY_FILTER_INPUT_NAME]);
        
        $query = self::textSearchByCompanyEmail($query, $post[Company::COMPANY_FILTER_INPUT_NAME]);
        
        $query = self::textSearchByUserEmail($query, $post[Company::COMPANY_FILTER_INPUT_NAME]);
        
        $query = self::textSearchByCompanyCode($query, $post[Company::COMPANY_FILTER_INPUT_NAME]);
        
        $query = self::textSearchByCompanyAdress($query, $post[Company::COMPANY_FILTER_INPUT_NAME]);
        
        $query = self::textSearchByCompanyVatCode($query, $post[Company::COMPANY_FILTER_INPUT_NAME]);
        
        $query = self::textSearchByPhoneNumber($query, $post[Company::COMPANY_FILTER_INPUT_NAME]);
        
        $query = self::extendedSearchByClass($query, $post['User']['class']);
        
        $query = self::extendedSearchByCompanyDocument($query, $post['CompanyDocument']['documentActivity']);
        
        $query = self::extendedSearchByUserActiveService($query, $post['UserServiceActive']['status']);
        
        $query = self::extendedSearchByUserService($query, $post['UserService']['paid']);
        
        $query = self::extendedSearchByInvitedUsers($query, $post['Company']['invitedUsers']);
        
        $query = self::extendedSearchByCompanyStatus($query, $post['Company']['companyStatus']);
        
        $query = self::extendedSearchByUserLastLogin($query, $post['User']['start_last_login'], $post['User']['end_last_login']);
        
        $query = self::extendedSearchByUserCreatedAt($query, $post['User']['start_created_at'], $post['User']['end_created_at']);
        
        $query = self::extendedSearchByUserSubscribtionEnding($query, $post['UserServiceActive']['dateSubscribeFrom'], $post['UserServiceActive']['dateSubscribeTo']);
        
        $query = self::extendedSearchByCountry($query, $post['Company']['city_id']);
        
        $query = self::extendedSearchByPotential($query, $post['Company']['potential']);
		
		$query = self::extendedSearchByRegistrationReason($query, $post['User']['came_from_id']);
        
        $query->groupBy(['company.id']);
        
        $query->orderBy(['company.created_at' => SORT_DESC]);
        
        return $query;
    }
    
    /**
     * Gets companies with related tables for search function
     * 
     * @return ActiveQuery companies list with related tables
     */
    public static function getCompaniesQuery() 
    {
        return $query = self::find()
                ->joinWith('ownerList')
                ->joinWith('ownerList.userServiceActive as ownerActiveServiceAlias')
                ->joinWith('ownerList.userService as ownerServiceAlias')
				->joinWith('ownerList.cameFrom')
                ->joinWith('companyUsers')
                ->joinWith('companyUsers.user')
                ->joinWith('companyUsers.user.userServiceActive as userServiceActiveAlias')
                ->joinWith('companyUsers.user.userService as userServiceAlias')
                ->joinWith('companyDocuments as companyDocumentsAlias')
                ->joinWith('city');
    }
    
    /**
     * Filters companies by given class for search function from given query
     * 
     * @param ActiveQuery $query data query gyven by search function
     * @param string $postData data from post
     * @return ActiveQuery formed query
     */
    public static function extendedSearchByClass($query, $postData = null) 
    {
        return $query->andFilterWhere(['ownerList.class' => $postData]);
    }
    
    /**
     * Finds companies by given company document status for search function from given query
     * 
     * @param ActiveQuery $query data query gyven by search function
     * @param string $postData data from post
     * @return ActiveQuery formed query
     */
    public static function extendedSearchByCompanyDocument($query, $postData = null) 
    {
        if ($postData) {
            $query->andWhere(['>', 'companyDocumentsAlias.date', time()]);
        }
        
        if ($postData != '' && ($postData == CompanyDocument::NOT_ACTIVE))
        {
            $query->andWhere(['<', 'companyDocumentsAlias.date', time()]);
        }
        return $query;
    }
    
    /**
     * Filters companies by given user service active status for search function from given query
     * 
     * @param ActiveQuery $query data query gyven by search function
     * @param string $postData data from post
     * @return ActiveQuery formed query
     */
    public static function extendedSearchByUserActiveService($query, $postData = null)
    {   
        if ($postData === '') {
            return $query;
        }
        if (!$postData) {
            $query->andWhere(['and', ['ownerActiveServiceAlias.id' => null], ['userServiceActiveAlias.id' => null]]);  
        }
        if ($postData) {
            $query->andWhere(['or', ['not', ['ownerActiveServiceAlias.id' => null]], ['not', ['userServiceActiveAlias.id' => null]]]);  
        }
        return $query;
    }
    
    /**
     * Finds companies by given user service status for search function from given query
     * 
     * @param ActiveQuery $query data query gyven by search function
     * @param string $postData data from post
     * @return ActiveQuery formed query
     */
    public static function extendedSearchByUserService($query, $postData = null)
    {
        if ($postData == UserService::PAID) {
            $query->andFilterWhere(['or', ['ownerServiceAlias.paid' => $postData], ['userServiceAlias.paid' => $postData]]);
        }
        
        if ($postData != '' && ($postData == UserService::SEARCH_NOT_PAID)) {
            $userServicePaidArray = self::findUsersWithPaidSubscription();
            $query->andWhere([
                'and', [
                    'not in', 'ownerList.id', $userServicePaidArray], 
                ['or', 
                    ['company_user.user_id' => null], 
                    ['not in', 'company_user.user_id', $userServicePaidArray]]]);
        }
        return $query;
    }
    
    /**
     * Finds users who had paid for any kind of subscription
     * 
     * @return ActiveQuery
     */
    public static function findUsersWithPaidSubscription()
    {
        return UserService::find()
                    ->select('user_service.user_id as id')
                    ->where(['user_service.paid' => UserService::SEARCH_PAID])
                    ->groupBy('user_service.user_id');
    }
    
    /**
     * Finds companies who have invited users
     * 
     * @param ActiveQuery $query data query gyven by search function
     * @param string $postData data from post
     * @return ActiveQuery formed query
     */
    public static function extendedSearchByInvitedUsers($query, $postData = null)
    {
        if ($postData == self::HAS_INVITED_USERS) 
        {
            $query->andWhere(['not', ['company_user.user_id' => null]]);
        }
        
        if ($postData != '' && ($postData == self::HAS_NOT_INVITED_USERS))
        {
            $query->andWhere(['company_user.user_id' => null]);
        }
        return $query;
    }
    
    /**
     * Finds companies with given activity status for search
     * 
     * @param ActiveQuery $query data query gyven by search function
     * @param string $postData data from post
     * @return ActiveQuery formed query
     */
    public static function extendedSearchByCompanyStatus($query, $postData = null)
    {
        if($postData == self::IS_ARCHIVED) 
        {
            $query->andFilterWhere(['company.archive' => self::ARCHIVED]);
        }
        
        if($postData == self::IS_ACTIVE) 
        {
            $query->andFilterWhere(['company.active' => self::ACTIVE]);
        }
        return $query;
    }
    
    /**
     * Finds companies whos users are in given range by last login date
     * 
     * @param ActiveQuery $query data query gyven by search function 
     * @param string|null $dateDataFrom starting date range or null 
     * @param string|null $dateDataTo end date or null
     * @return ActiveQuery formed query
     */
    public static function extendedSearchByUserLastLogin($query, $dateDataFrom = null, $dateDataTo = null)
    {
        if (!empty($dateDataFrom)) {
            $dateLastLoginFrom = strtotime($dateDataFrom . ' 00:00:00 Europe/Vilnius');
        } else {
            $dateLastLoginFrom = strtotime($dateDataFrom);
        }

        if (!empty($dateDataTo)) {
            $dateLastLoginTo = strtotime($dateDataTo . ' 23:59:59 Europe/Vilnius');
        } else {
            $dateLastLoginTo = strtotime($dateDataTo);
        }

        if ($dateLastLoginFrom == 0 && $dateLastLoginTo == 0) {
            return $query;
        }
        
        if ($dateLastLoginFrom != 0 && $dateLastLoginTo != 0) {
            $query->andWhere([
                'or', 
                ['between', 'ownerList.last_login', $dateLastLoginFrom, $dateLastLoginTo], 
                ['between', 'user.last_login', $dateLastLoginFrom, $dateLastLoginTo]
            ]);
            return $query;
        }
        
        if ($dateLastLoginFrom == 0 || $dateLastLoginTo == 0) {
            $query->andWhere([
                'or', 
                ['or', 
                    ['>=', 'ownerList.last_login', $dateLastLoginFrom], 
                    ['<=', 'ownerList.last_login', $dateLastLoginTo]],
                ['or', 
                    ['>=', 'user.last_login', $dateLastLoginFrom], 
                    ['<=', 'user.last_login', $dateLastLoginTo]]]);
            return $query;
        }
        
        return $query;
    }
    
    /**
     * Finds companies whos users are in given range by when user account was created
     * 
     * @param ActiveQuery $query data query gyven by search function 
     * @param string|null $dateDataFrom starting date range or null 
     * @param string|null $dateDataTo end date or null
     * @return ActiveQuery formed query
     */
    public static function extendedSearchByUserCreatedAt($query, $dateDataFrom = null, $dateDataTo = null)
    {
        if (!empty($dateDataFrom)) {
            $dateCreatedAtFrom = strtotime($dateDataFrom . ' 00:00:00 Europe/Vilnius');
        } else {
            $dateCreatedAtFrom = strtotime($dateDataFrom);
        }

        if (!empty($dateDataTo)) {
            $dateCreatedAtTo = strtotime($dateDataTo . ' 23:59:59 Europe/Vilnius');
        } else {
            $dateCreatedAtTo = strtotime($dateDataTo);
        }
        
        if ($dateCreatedAtFrom == 0 && $dateCreatedAtTo == 0) {
            return $query;
        }
        
        if ($dateCreatedAtFrom != 0 && $dateCreatedAtTo != 0) {
            $query->andWhere([
                'or',
                ['between', 'ownerList.created_at', $dateCreatedAtFrom, $dateCreatedAtTo], 
                ['between', 'user.created_at', $dateCreatedAtFrom, $dateCreatedAtTo]]);
            return $query;
        }
        
        if ($dateCreatedAtFrom == 0 || $dateCreatedAtTo == 0) {
            $query->andWhere([
                'or', 
                ['or', 
                    ['>=', 'ownerList.created_at', $dateCreatedAtFrom], 
                    ['<=', 'ownerList.created_at', $dateCreatedAtTo]], 
                ['or', 
                    ['>=', 'user.created_at', $dateCreatedAtFrom], 
                    ['<=', 'user.created_at', $dateCreatedAtTo]]]);
            return $query;
        }
        
        return $query;
    }
    
    /**
     * Finds companies which users are in given range by subscription ending date
     * 
     * @param ActiveQuery $query data query given by search function 
     * @param string|null $dateDataFrom Date range beginning date or null 
     * @param string|null $dateDataTo Date range ending date or null
     * @return ActiveQuery formed query
     */
    public static function extendedSearchByUserSubscribtionEnding($query, $dateDataFrom = null, $dateDataTo = null)
    {
        $dateSubscribedFrom = strtotime($dateDataFrom);
        $dateSubscribedTo = strtotime($dateDataTo);
        
        if ($dateSubscribedFrom == 0 && $dateSubscribedTo == 0) {
            return $query;
        }
        
        if ($dateSubscribedFrom != 0 && $dateSubscribedTo != 0) {
            return $query->andWhere([
                'or',
                ['between', 'ownerActiveServiceAlias.end_date', $dateSubscribedFrom, $dateSubscribedTo],
                ['between', 'userServiceActiveAlias.end_date', $dateSubscribedFrom, $dateSubscribedTo]
            ]);
        }
        
        return $query;
    }
    
    /**
     * extended search condintion to find by country
     * 
     * @param ActiveQuery $query data query given by search function
     * @param string $country country letters
     * @return ActiveQuery formed query
     */
    public static function extendedSearchByCountry($query, $country)
    {   
        if($country) {
            $query->andWhere([City::tableName(). '.country_code' => $country]);
        }
        return $query;
    }
    
    /**
     * extended search condintion to find by potential
     * 
     * @param ActiceQuery $query data query given by search function
     * @param integer $potential user selected id 
     * @return ActiveQuery 
     */
    public static function extendedSearchByPotential($query, $potential)
    {
        $query->andFilterWhere([self::tableName() . '.potential' => $potential]);
        return $query;
    }
    
    /**
     * extended search condintion to find by reason
     * 
     * @param ActiveQuery $query data query given by search function
     * @param type $reason user selected id
     * @return ActiveQuery
     */
    public static function extendedSearchByRegistrationReason($query, $reason)
    {
        return $query->andFilterWhere([CameFrom::tableName(). '.id' => $reason]);
    }
    
    /**
     * Finds companies by company name or company owner name or/and surname if company name does not exist
     * 
     * @param ActiveQuery $query data query gyven by search function
     * @param string $postData data from post 
     * @return ActiveQuery formed query
     */
    public static function textSearchByCompanyName($query, $postData = null)
    {
        $data = explode(' ', $postData);
        if (count($data) > 1) {
            $query->filterWhere(['and', 
                ['or',
                ['like',
                    'company.title', $data[0]],
                ['like',
                    'ownerList.name', $data[0]],
                ['like',
                    'ownerList.surname', $data[0]],
                ],
                ['or',
                ['like',
                    'company.title', $data[1]], 
                ['like',
                    'ownerList.name', $data[1]],
                ['like',
                    'ownerList.surname', $data[1]],
                ]
            ]);
        } else {
            $query->filterWhere(['or',
                ['like',
                    'company.title', $data[0]], 
                ['like',
                    'ownerList.name', $data[0]],
                ['like',
                    'ownerList.surname', $data[0]],
            ]);
        }
        return $query;
    }
    
    /**
     * Filters companies by user name or/and surname
     * 
     * @param ActiveQuery $query data query gyven by search function
     * @param string $postData data from post 
     * @return ActiveQuery formed query
     */
    public static function textSearchByCompanyUsers($query, $postData = null)
    {
        $data = explode(' ', $postData);
        if (count($data) > 1) {
            $query->orFilterWhere(['and', 
                ['or',
                ['like',
                    'user.name', $data[0]], 
                ['like',
                    'user.surname', $data[0]],
                ],
                ['or',
                ['like',
                    'user.name', $data[1]], 
                ['like',
                    'user.surname', $data[1]],
                ]
            ]);
        } else {
            $query->orfilterWhere(['or',
                ['like',
                    'user.name', $data[0]], 
                ['like',
                    'user.surname', $data[0]],
            ]);
        }
        return $query;
    }
    
    /**
     * Filters companies by  company email
     * 
     * @param ActiveQuery $query data query gyven by search function
     * @param string $postData data from post 
     * @return ActiveQuery formed query
     */
    public static function textSearchByUserEmail($query, $postData = null)
    {
        return $query->orFilterWhere(['or', ['like', 'ownerList.email', $postData], ['like', 'user.email', $postData]]);
    }
    
    /**
     * Filters companies by user email
     * 
     * @param ActiveQuery $query data query gyven by search function
     * @param string $postData data from post 
     * @return ActiveQuery formed query
     */
    public static function textSearchByCompanyEmail($query, $postData = null)
    {
        return $query->orFilterWhere(['like', 'company.email', $postData]);
    }
    
    /**
     * Filters companies users by email
     * 
     * @param string $request
     * @return array
     */
    public static function getCompanyUserByEmail($request)
    {
        return self::find()
            ->from(self::tableName())
            ->where(['company.email' => $request])
            ->orderBy(['company.created_at' => SORT_DESC])
            ->all();
    }
    
    /**
     * Finds companies by company code
     * 
     * @param ActiveQuery $query data query gyven by search function
     * @param string $postData data from post 
     * @return ActiveQuery formed query
     */
    public static function textSearchByCompanyCode($query, $postData = null)
    {
        return $query->orFilterWhere(['company.code' => $postData]);
    }
    
    /**
     * Finds companies by company vat code
     * 
     * @param ActiveQuery $query data query gyven by search function
     * @param string $postData data from post 
     * @return ActiveQuery formed query
     */
    public static function textSearchByCompanyVatCode($query, $postData = null)
    {
        return $query->orFilterWhere(['company.vat_code' => $postData]);
    }
    
    /**
     * Finds companies by company phone
     * 
     * @param ActiveQuery $query data query gyven by search function
     * @param string $postData data from post 
     * @return ActiveQuery formed query
     */
    public static function textSearchByPhoneNumber($query, $postData = null)
    {
        return $query->orFilterWhere(['company.phone' => $postData]);
    }
    
    /**
     * Finds companies by company address
     * 
     * @param ActiveQuery $query data query gyven by search function
     * @param string $postData data from post 
     * @return ActiveQuery formed query
     */
    public static function textSearchByCompanyAdress($query, $postData = null)
    {
        return $query->orFilterWhere(['like', 'company.address', $postData]);
    }
    
    /**
     * Gets client name string from company owner list
     * 
     * @return string 
     */
    public function getClientNameString()
    {
        return $this->ownerList->name . ' ' . $this->ownerList->surname . ', ';
    }
    
    /**
     * Gets client company class (carrier or supplier)
     * 
     * @return string|null 
     */
    public function getCompanyClassType() 
    {
        switch ($this->ownerList->class) {
            case User::CARRIER:
                $class = Yii::t('element', 'A-C-22b');
                break;
            case User::MINI_CARRIER:
                $class = Yii::t('app', 'CLASS_LABEL_MINI_CARRIER');
                break;
            default:
                $class = Yii::t('element', 'A-C-22a');
        }
        return Yii::t('element', 'A-C-22', ['companyClass' => $class]);
    }
    
    /**
     * Gets company activity type
     * 
     * @return string
     */
    public function getCompanyActivityType()
    {
        if ($this->archive == 1) {
            return Yii::t('element', 'A-C-29a');
        }
        
        if ($this->active == 1) {
            return Yii::t('element', 'A-C-29');
        }

        return Yii::t('yii', '(not set)');
    }
    
    /**
     * Returns list of translated invited users for company options, extended search
     *
     * @return array
     */
    public function getTranslatedInvitedUsers()
    {
        return [
            self::HAS_INVITED_USERS => Yii::t('app', 'COMPANY_INVITED_USER_HAD'),
            self::HAS_NOT_INVITED_USERS => Yii::t('app', 'COMPANY_NO_INVITED_USER'),
        ];
    }
    
    /**
     * Returns list of translated company active type, extended search
     *
     * @return array
     */
    public function getTranslatedCompanyActivityType()
    {
        return [
            self::IS_ACTIVE => Yii::t('app', 'COMPANY_IS_ACTIVE'),
            self::IS_ARCHIVED => Yii::t('app', 'COMPANY_IS_ARCHIVED'),
        ];
    }

    /**
     * Gets all users related to company for drop down list selection
     */
    public function getAllCompanyUsersForDropDownList() 
    {
        $userList = [];
        $userName = $this->ownerList->name . ' ' . $this->ownerList->surname;
        $userList[$this->ownerList->id] = $userName . ' (ID: ' . $this->ownerList->id . ')';
        foreach($this->companyUsers as $companyUser) {
            $userName = $companyUser->user->name . ' ' . $companyUser->user->surname;
            $userList[$companyUser->user->id] = $userName . ' (ID: ' . $companyUser->user->id . ')';
        }
        return $userList;
    }
    
    /**
     * Returns currently active phone code and number
     *
     * @return array
     */
    public function getActivePhoneNumber()
    {
        if (is_null($this->phone) || empty($this->phone)) {
            return CountryPhoneCode::getActivePhoneNumber(null, null);
        }

        return CountryPhoneCode::splitToCodeAndNumber($this->phone);
    }
    
    /**
     * Changes company owner Id in database
     * 
     * @return boolean success
     */
    public function changeCompanyOwnerId($newOwnerId) 
    {
        $this->owner_id = $newOwnerId;
        return $this->update();
    }
    
    /**
     * Changes company information
     * 
     * @return boolean
     */
    public function changeCompanyInfo($post)
    {
        $isUpdateSuccessful = true;
        if ($this->isAttributeChanged('code', false) && $isUpdateSuccessful)  {
            $isUpdateSuccessful = true;
        }
        if ($this->isAttributeChanged('email', false) && $isUpdateSuccessful)  {
            $isUpdateSuccessful = true;
        }
        if (($this->archive == self::ARCHIVED) && ($post['Company']['visible'] == self::VISIBLE)) {
            $isUpdateSuccessful = false;
        }
        return $isUpdateSuccessful && $this->update([
            'vat_code',
            'address',
            'city_id',
            'phone',
        ]);
    }
    
    /**
     * Checks whether VAT code length is valid
     *
     * @param string $vatCode VAT code that needs to be checked
     * @return null|string
     */
    public static function isVatCodeLengthValid($vatCode)
    {
        if (empty($vatCode) || strlen($vatCode) <= self::VAT_CODE_MIN_LENGTH) {
            return null;
        }
        return $vatCode;
    }
    
    /**
     * Returns information about user/company from European Commission by given VAT code
     *
     * @param string $code Country code (two digital letters of country name)
     * @param integer $number VAT code number without country code
     * @return array
     */
    public static function getInfoFromECByVatCode($code, $number)
    {
        $soapClient = new SoapClient(Yii::$app->params['VATService']);
        return (array) $soapClient->checkVat([
            'countryCode' => $code,
            'vatNumber' => $number,
        ]);
    }
    
    /**
     * Returns split VAT code in two pieces: country code and VAT code numbers
     *
     * @param string $code VAT code
     * @param integer $length Country code length
     * @return array
     */
    public static function splitVatCode($code, $length)
    {
        $codeLength = strlen($code);
        return [substr($code, 0, $length), substr($code, $length, $codeLength)];
    }

    /**
     * Finds user company regardless of whether the user is the owner of the company or only company worker
     *
     * @param null|integer $id User ID
     * @return self|null|ActiveRecord
     */
    public static function findUserCompany($id)
    {
        return self::find()
            ->from(self::tableName(). ' company')
            ->joinWith('companyUsers company_user')
            ->where(['company.owner_id' => $id])
            ->orWhere(['company_user.user_id' => $id])
            ->one();
    }

    /**
     * Sets company model scenario depending on company account type
     */
    public function setScenarioByAccountType()
    {
        if ($this->ownerList->account_type == User::NATURAL) {
            $this->scenario = self::SCENARIO_CONTACT_INFO_NATURAL;
        } else {
            $this->scenario = self::SCENARIO_CONTACT_INFO_LEGAL;
        }
    }

    /**
     * Formats user phone number
     */
    public function formatPhone()
    {
        if (substr($this->phone, 0, 1) == 8) {
            $this->phone = substr_replace($this->phone, '+370', 0, 1); // Replace first character which is 8 to +370
        }

        if (!$this->validate(['phone'])) {
            $this->phone = '+37061234567'; // Phone number is still invalid, replace it with default one
        }
    }
    
    /**
     * Returns companies list data provider
     *
     * @param ActiveQuery $query Companies query
     * @return ActiveDataProvider
     */
    public static function getCompaniesDataProvider($query)
    {
        return new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
        ]);
    }
    
    /**
     * Returns specific user activity status
     * 
     * @param integer $isArchived
     * @param integer $isActive
     * @return string
     */
    public static function getCompanyUserActivityStatus($isArchived, $isActive)
    {
        if ($isArchived == User::ARCHIVED_USER) {
            return Yii::t('text', 'CLIENT_EXPORT_COMPANY_ACTIVITY_ARCHIVED');
        } else if ($isActive == User::ACTIVE_USER) {
            return Yii::t('text', 'CLIENT_EXPORT_COMPANY_ACTIVITY_ACTIVE');
        } else {
            return Yii::t('text', 'CLIENT_EXPORT_COMPANY_ACTIVITY_INACTIVE');
        }
    }

    /**
     * Checks whether company is archived
     *
     * @return boolean
     */
    public function isArchived()
    {
        return $this->archive == self::ARCHIVED;
    }
    
    /**
     * Checks whether company is active
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->active == self::ACTIVE;
    }

    /**
     * Returns all company users IDs
     *
     * List of IDs consist of company owner ID and rest of the company users.
     *
     * @return array
     */
    public function getAllUsersIds()
    {
        $ids = [$this->owner_id];
        foreach ($this->companyUsers as $companyUser) {
            array_push($ids, $companyUser->user_id);
        }

        return $ids;
    }

    /**
     * Archives company or multiple companies
     *
     * @param integer|array $id List of companies IDs or specific company ID to be archived
     * @return integer Number of archived companies
     */
    public static function archive($id)
    {
        return self::updateAll([
            'active' => self::INACTIVE,
            'archive' => self::ARCHIVED,
            'visible' => self::INVISIBLE,
        ], compact('id'));
    }

    /**
     * Unarchives company or multiple companies
     *
     * @param integer|array $id List of companies IDs or specific company ID to be unarchived
     * @return integer Number of unarchived companies
     */
    public static function unarchive($id)
    {
        return self::updateAll([
            'active' => self::ACTIVE,
            'archive' => self::NOT_ARCHIVED,
            'visible' => self::VISIBLE,
        ], compact('id'));
    }
    
    /**
     * Activates company or multiple companies
     *
     * @param integer|array $id List of companies IDs or specific company ID to be activated
     * @return integer Number of activated companies
     */
    public static function activeCompany($id)
    {
        return self::updateAll([
            'active' => self::ACTIVE,
            'archive' => self::NOT_ARCHIVED,
            'visible' => self::VISIBLE,
        ], compact('id'));
    }

    /**
     * Returns translated company archive values
     *
     * @return array
     */
    public static function getTranslatedArchives()
    {
        return [
            self::NOT_ARCHIVED => Yii::t('app', 'NOT_ARCHIVED'),
            self::ARCHIVED => Yii::t('app', 'ARCHIVED'),
        ];
    }
    
    public function saveCompanyPotentiality($status)
    {
        $status = $status === 'false' ? self::NOT_POTENTIAL : self::POTENTIAL;
        $this->scenario = self::SCENARIO_CHANGE_COMPANY_POTENTIALITY;       
        $this->potential = $status;
        $this->save();
    }
	
	public static function potentialitySelect()
    {
        return [
            self::POTENTIAL => Yii::t('app', 'COMPANY_POTENTIAL'),
            self::NOT_POTENTIAL => Yii::t('app', 'COMPANY_NOT_POTENTIAL'),
        ];
    }
	
	public static function reasonsSelect()
    {
        return ArrayHelper::map(CameFrom::find()
                ->where(['type' => CameFrom::REASON_TO_REGISTER])->all(), 'id', 'source_name');
    }
}
