<?php

namespace common\models;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\components\audit\Log;
use common\components\audit\SystemMessage;
use common\components\MailLanguage;
use kartik\icons\Icon;
use SoapClient;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\bootstrap\Html;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\IdentityInterface;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $surname
 * @property string $email
 * @property string $phone
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $password_expires
 * @property integer $class
 * @property integer $original_class
 * @property integer $account_type
 * @property string $came_from_referer
 * @property string $personal_code
 * @property string $company_code
 * @property string $company_name
 * @property integer $city_id
 * @property string $address
 * @property double $vat_code
 * @property integer $came_from_id
 * @property integer $current_credits
 * @property integer $active
 * @property integer $allow
 * @property integer $archive
 * @property integer $visible
 * @property integer $suggestions
 * @property string $suggestions_token
 * @property string $last_login
 * @property string $warning_sent
 * @property string $blocked_until
 * @property string $token
 * @property string $created_at
 * @property string $updated_at
 * @property int $service_credits
 *
 * @property AdminAsUser[] $adminAsUsers
 * @property Company[] $companies
 * @property CompanyUser $companyUser
 * @property Load[] $loads
 * @property LoadPreview[] $loadPreviews
 * @property City $city
 * @property CameFrom $cameFrom
 * @property UserLanguage[] $userLanguages
 * @property UserLog[] $userLogs
 * @property UserService[] $userServices
 * @property UserServiceActive[] $userServiceActives
 */
class User extends ActiveRecord implements IdentityInterface
{
    /** @const boolean User gets suggestions to email */
    const SEND_SUGGESTIONS = 1;
    
    /** @const boolean User does not get suggestions to email */
    const DO_NOT_SEND_SUGGESTIONS = 0;
    
    /** @const boolean User account is active */
    const ACTIVE = 1;

    /** @const boolean User account is inactive */
    const INACTIVE = 0;

    /** @const boolean User is allowed to log-in to system */
    const ALLOWED = 1;

    /** @const boolean User is not allowed to log-in to system */
    const FORBIDDEN = 0;

    /** @const boolean User account is archived */
    const ARCHIVED = 1;

    /** @const boolean User account is not archived */
    const NOT_ARCHIVED = 0;

    /** @const boolean User account is visible to other users */
    const VISIBLE = 1;

    /** @const boolean User account is not visible to other users */
    const INVISIBLE = 0;

    /** @const integer Minimum length of user name */
    const NAME_MIN_LENGTH = 2;

    /** @const integer Maximum length of user name */
    const NAME_MAX_LENGTH = 255;

    /** @const integer Minimum length of user surname */
    const SURNAME_MIN_LENGTH = 2;

    /** @const integer Maximum length of user surname */
    const SURNAME_MAX_LENGTH = 255;

    /** @const integer Maximum length of user email */
    const EMAIL_MAX_LENGTH = 255;

    /** @const integer Maximum length of user authentication key */
    const AUTH_KEY_MAX_LENGTH = 32;

    /** @const null Default password reset token value */
    const DEFAULT_PASSWORD_RESET_TOKEN = null;

    /** @const integer Minimum length of user password */
    const PASSWORD_MIN_LENGTH = 6;

    /** @const integer Maximum length of user password */
    const PASSWORD_MAX_LENGTH = 255;

    /** @const integer User type is mini carrier (mini-vežėjas) */
    const MINI_CARRIER = 2;

    /** @const integer User type is carrier (vežėjas) */
    const CARRIER = 1;

    /** @const integer User type is supplier (tiekėjas) */
    const SUPPLIER = 0;

    /** @const null Default class value */
    const DEFAULT_CLASS = null;

    /** @const null Default original class value */
    const DEFAULT_ORIGINAL_CLASS = null;

    /** @const integer User account type is natural (fizinis) */
    const NATURAL = 0;

    /** @const integer User account type is legal (juridinis) */
    const LEGAL = 1;

    /** @const null Default original type value */
    const DEFAULT_ACCOUNT_TYPE = null;

    /** @const null Default city ID value */
    const DEFAULT_CITY_ID = null;

    /** @const null Default VAT code value */
    const DEFAULT_VAT_CODE = null;

    /** @const null Default came from ID value */
    const DEFAULT_CAME_FROM_ID = null;

    /** @const null Default current credits value */
    const DEFAULT_CURRENT_CREDITS = null;

    /** @const integer Minimum number of current credits that user can have */
    const CURRENT_CREDITS_MIN_VALUE = 0;

    /** @const null Default personal code value */
    const DEFAULT_PERSONAL_CODE = null;

    /** @const null Default company code value */
    const DEFAULT_COMPANY_CODE = null;

    /** @const null Default company name value */
    const DEFAULT_COMPANY_NAME = null;

    /** @const integer Maximum length of user address */
    const ADDRESS_MAX_LENGTH = 512;

    /** @const null Default address value */
    const DEFAULT_ADDRESS = null;

    /** @const null Default warning sent attribute value */
    const DEFAULT_WARNING_SENT = null;

    /** @const null Default blocked until attribute value */
    const DEFAULT_BLOCKED_UNTIL = null;

    /** @const integer Number of characters that token MUST have */
    const TOKEN_LENGTH = 64;

    /** @const null Default token value */
    const DEFAULT_TOKEN = null;

    /** @const boolean Agree with rules value */
    const AGREE_WITH_RULES = true;

    /** @const boolean Disagree with rules value */
    const NOT_AGREE_WITH_RULES = false;

    /** @const integer Minimum number of characters that VAT code MUST have */
    const VAT_CODE_MIN_LENGTH = 2;

    /** @const integer Minimum number of characters that change email text MUST have */
    const CHANGE_EMAIL_TEXT_MIN_LENGTH = 2;

    /** @const integer Maximum number of characters that change email text MUST have */
    const CHANGE_EMAIL_TEXT_MAX_LENGTH = 3000;

    /** @const boolean Email must be send to user */
    const SEND_EMAIL = true;

    /** @const boolean Email must not be send to user */
    const NOT_SENT_EMAIL = false;
    
    /** $const string cron job identification token */
    const SEND_SUGGESTIONS_TOKEN = 'YK8f1hsALxmHtLuHuB_y5wj9aLeZdozKUAzrOK1xGfasW5jyuk75_XwCHgvasfYV';

    /** @const string Model scenario when user signs up to site */
    const SCENARIO_SIGN_UP_CLIENT = 'sign-up-client';

    /** @const string Model scenario when sign up form data should be saved to database */
    const SCENARIO_SIGN_UP_SERVER = 'sign-up-server';

    /** @const string Model scenario when user confirms provided email */
    const SCENARIO_CONFIRM_SIGN_UP = 'confirm-sign-up';

    /** @const string Model scenario when user signs up to site through invitation link */
    const SCENARIO_SIGN_UP_INVITATION_CLIENT = 'sign-up-invitation-client';

    /** @const string Model scenario when user sign up by invitation form data must be saved to database */
    const SCENARIO_SIGN_UP_INVITATION_SERVER = 'sign-up-invitation-server';

    /** @const string Model scenario when user tries to login to website */
    const SCENARIO_USER_LOGINS = 'user-logins';

    /** @const string Model scenario when system logins user to website */
    const SCENARIO_SYSTEM_LOGINS_USER = 'system-logins-user';

    /** @const string Model scenario when user requests to reset password */
    const SCENARIO_USER_REQUESTS_PASSWORD_RESET = 'user-requests-password-reset';

    /** @const string Model scenario when server processes user request to reset password */
    const SCENARIO_SERVER_PROCESS_USER_PASSWORD_RESET_REQUEST = 'server-process-user-password-reset-request';

    /** @const string Model scenario when user reset password */
    const SCENARIO_RESET_PASSWORD_CLIENT = 'reset-password-client';

    /** @const string Model scenario when user password should be reset */
    const SCENARIO_RESET_PASSWORD_SERVER = 'reset-password-server';

    /** @const string Model scenario when user edits personal data information */
    const SCENARIO_EDIT_MY_DATA_CLIENT = 'edit-my-data-client';

    /** @const string Model scenario when user profile changes should be saved to database */
    const SCENARIO_EDIT_MY_DATA_SERVER = 'edit-my-data-server';

    /** @const string Model scenario when user wants to change email */
    const SCENARIO_CHANGE_EMAIL = 'change-email';

    /** @const string Model scenario when user edits company data information */
    const SCENARIO_EDIT_COMPANY_DATA_CLIENT = 'edit-company-data-client';

    /** @const string Model scenario when user company data changes should be saved to database */
    const SCENARIO_EDIT_COMPANY_DATA_SERVER = 'edit-company-data-server';

    /** @const string Model scenario when user changes password */
    const SCENARIO_CHANGE_PASSWORD_CLIENT = 'change-password-client';

    /** @const string Model scenario when changed user password should be saved to database */
    const SCENARIO_CHANGE_PASSWORD_SERVER = 'change-password-server';

    /** @const string Model scenario when updating current credits */
    const SCENARIO_UPDATE_CURRENT_CREDITS = 'update-current-credits';

    /** @const string Model scenario when updating advertisement credits */
    const SCENARIO_UPDATE_SERVICE_CREDITS = 'update-service-credits';

    /** @const string Model scenario when administrator uses extended search filter to find clients  */
    const SCENARIO_EXTENDED_CLIENT_SEARCH = 'extended-client-search';
    
    /** @const string Model scenario when changing company status  */
    const SCENARIO_CHANGE_COMPANY_STATUS_SERVER = 'change-company-status-server';
    
    /** @const string Model scenario when changing company class  */
    const SCENARIO_CHANGE_COMPANY_CLASS = 'change-company-class-client';

    /** @const string Model scenario when administrator edits company user information */
    const SCENARIO_ADMIN_EDITS_COMPANY_USER = 'admin-edits-company-user';

    /** @const string Model scenario when system updates company user information */
    const SCENARIO_SYSTEM_UPDATES_COMPANY_USER = 'system-updates-company-user';

    /** @const string Model scenario when administrator adds new company user */
    const SCENARIO_ADMIN_ADDS_NEW_COMPANY_USER = 'admin-adds-new-company-user';

    /** @const string Model scenario when system saves new company user */
    const SCENARIO_SYSTEM_SAVES_NEW_COMPANY_USER = 'system-saves-new-company-user';

    /** @const string Model scenario when system migrates company owner data from one database to another */
    const SCENARIO_SYSTEM_MIGRATES_COMPANY_OWNER_DATA = 'system-migrates-company-owner-data';

    /** @const string Model scenario when system migrates company user data from one database to another */
    const SCENARIO_SYSTEM_MIGRATES_COMPANY_USER_DATA = 'system-migrates-company-user-data';
    
    /** @const string Model scenario when user does not want to get suggestions to email */
    const SCENARIO_REJECT_SUGGESTIONS = 'reject-suggestions';

    const SCENARIO_SYSTEM_MIGRATES_USER = 'system-migrates-user';

    /** @const string Model scenario when system makes user as supplier */
    const SCENARIO_SYSTEM_MAKES_USER_AS_SUPPLIER = 'system-makes-user-as-supplier';

    /** @const string Model scenario when a guest is buying creditcodes */
    const SCENARIO_SYSTEM_MAKES_CREDITBUYER= 'system-makes-creditbuyer';

    /** @const integer Status if user is archived */
    const ARCHIVED_USER = 1;

    /** @const integer Status if user is active */
    const ACTIVE_USER = 1;
    
    /** @var string User password */
    public $password;

    /** @var string Repeated user password */
    public $repeatPassword;

    /** @var string Current user password */
    public $currentPassword;

    /** @var string New User password */
    public $newPassword;

    /** @var string Repeated new user password */
    public $repeatNewPassword;

    /** @var array Language or languages that user speaks */
    public $language;

    /** @var boolean Attribute, whether user agrees with site rules */
    public $rulesAgreement = self::NOT_AGREE_WITH_RULES;

    /** @var integer City ID when user account type is natural */
    public $cityIdNatural;

    /** @var integer City ID when user account type is legal */
    public $cityIdLegal;

    /** @var string User address when account type is natural */
    public $addressNatural;

    /** @var string User address when account type is legal */
    public $addressLegal;

    /** @var string VAT code when user account type is natural */
    public $vatCodeNatural;

    /** @var string VAT code when user account type is legal */
    public $vatCodeLegal;

    /** @var string User text when wants to change email */
    public $changeEmail;
    
    /** @var integer User last login date range start for extended search */
    public $start_last_login;
    
    /** @var integer User last login date range end for extended search */
    public $end_last_login;
    
    /** @var integer User created at date range start for extended search */
    public $start_created_at;
    
    /** @var integer User created at date range end for extended search */
    public $end_created_at;

    /** @var boolean Attribute, whether email must be send to user */
    public $sendEmail;

    /** @var integer Attribute for buy-credit-form  */
    public $creditCodeService;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
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
            self::SCENARIO_REJECT_SUGGESTIONS => [
                'suggestions',
                'suggestions_token',
                'email',
            ],
            self::SCENARIO_SIGN_UP_CLIENT => [
                'email',
                'name',
                'surname',
                'phone',
                'language',
                'password',
                'repeatPassword',
                'class',
                'account_type',
//                'personal_code',
                'company_code',
                'company_name',
                'cityIdNatural',
                'cityIdLegal',
                'addressNatural',
                'addressLegal',
                'vatCodeNatural',
                'vatCodeLegal',
                'came_from_id',
                'rulesAgreement',
            ],
            self::SCENARIO_SIGN_UP_SERVER => [
                'name',
                'surname',
                'email',
                'phone',
                'auth_key',
                'password',
                'password_hash',
                'password_reset_token',
                'password_expires',
                'class',
                'original_class',
                'account_type',
                'personal_code',
                'company_code',
                'company_name',
                'city_id',
                'address',
                'vat_code',
                'came_from_id',
                'active',
                'allow',
                'archive',
                'visible',
                'last_login',
                'warning_sent',
                'blocked_until',
                'token',
                'created_at',
                'updated_at',
                'suggestions',
                'suggestions_token',
                'came_from_referer',
            ],
            self::SCENARIO_CONFIRM_SIGN_UP => [
                'token',
                'allow',
                'visible',
                'updated_at',
            ],
            self::SCENARIO_SIGN_UP_INVITATION_CLIENT => [
                'name',
                'surname',
                'phone',
                'language',
                'password',
                'repeatPassword',
                'rulesAgreement',
            ],
            self::SCENARIO_SIGN_UP_INVITATION_SERVER => [
                'name',
                'surname',
                'email',
                'phone',
                'auth_key',
                'password',
                'password_hash',
                'password_reset_token',
                'password_expires',
                'class',
                'original_class',
                'account_type',
                'personal_code',
                'company_code',
                'company_name',
                'city_id',
                'address',
                'vat_code',
                'came_from_id',
                'active',
                'allow',
                'archive',
                'visible',
                'last_login',
                'warning_sent',
                'blocked_until',
                'token',
                'suggestions',
                'suggestions_token',
            ],
            self::SCENARIO_USER_LOGINS => [
                'email',
                'password',
            ],
            self::SCENARIO_SYSTEM_LOGINS_USER => [
                'last_login',
                'active',
                'archive',
                'visible',
                'blocked_until',
            ],
            self::SCENARIO_USER_REQUESTS_PASSWORD_RESET => [
                'email',
            ],
            self::SCENARIO_SERVER_PROCESS_USER_PASSWORD_RESET_REQUEST => [
                'password_reset_token',
            ],
            self::SCENARIO_RESET_PASSWORD_CLIENT => [
                'password',
                'repeatPassword',
            ],
            self::SCENARIO_RESET_PASSWORD_SERVER => [
                'password_hash',
                'password_expires',
                'updated_at',
            ],
            self::SCENARIO_CHANGE_PASSWORD_CLIENT => [
                'currentPassword',
                'newPassword',
                'repeatNewPassword',
            ],
            self::SCENARIO_CHANGE_PASSWORD_SERVER => [
                'currentPassword',
                'newPassword',
                'repeatNewPassword',
                'password_hash',
                'password_expires',
                'updated_at',
            ],
            self::SCENARIO_EDIT_MY_DATA_CLIENT => [
                'name',
                'surname',
                'phone',
                'language',
            ],
            self::SCENARIO_EDIT_MY_DATA_SERVER => [
                'name',
                'surname',
                'phone',
            ],
            self::SCENARIO_EDIT_COMPANY_DATA_CLIENT => [
                'name',
                'surname',
                'company_code',
                'company_name',
                'address',
                'city_id',
            ],
            self::SCENARIO_EDIT_COMPANY_DATA_SERVER => [
                'name',
                'surname',
                'personal_code',
                'company_code',
                'company_name',
                'address',
                'city_id',
            ],
            self::SCENARIO_CHANGE_EMAIL => [
                'changeEmail',
            ],
            self::SCENARIO_UPDATE_CURRENT_CREDITS => [
                'current_credits',
            ],
            self::SCENARIO_UPDATE_SERVICE_CREDITS => [
                'service_credits',
            ],
            self::SCENARIO_EXTENDED_CLIENT_SEARCH => [
                'class',
                'start_last_login',
                'end_last_login',
                'last_login',
				'came_from_id',
                'start_created_at',
                'end_created_at'
            ],
            self::SCENARIO_CHANGE_COMPANY_STATUS_SERVER => [
                'archive',
                'active',
            ],
            self::SCENARIO_CHANGE_COMPANY_CLASS => [
                'class'
            ],
            self::SCENARIO_ADMIN_EDITS_COMPANY_USER => [
                'email',
                'name',
                'surname',
                'phone',
                'language',
                'password',
                'blocked_until',
                'archive',
				'came_from_id',
            ],
            self::SCENARIO_SYSTEM_UPDATES_COMPANY_USER => [
                'email',
                'name',
                'surname',
                'phone',
                'password_hash',
                'blocked_until',
                'active',
                'allow',
                'archive',
                'visible',
            ],
            self::SCENARIO_ADMIN_ADDS_NEW_COMPANY_USER => [
                'email',
                'name',
                'surname',
                'phone',
                'language',
                'password',
                'active',
                'sendEmail',
            ],
            self::SCENARIO_SYSTEM_SAVES_NEW_COMPANY_USER => [
                'name',
                'surname',
                'email',
                'phone',
                'auth_key',
                'password_hash',
                'password_expires',
                'active',
                'allow',
                'visible',
            ],
            self::SCENARIO_SYSTEM_MIGRATES_COMPANY_OWNER_DATA => [
                'id',
                'name',
                'surname',
                'email',
                'phone',
                'auth_key',
                'password_hash',
                'password_reset_token',
                'password_expires',
                'class',
                'original_class',
                'account_type',
                'personal_code',
                'company_code',
                'company_name',
                'city_id',
                'address',
                'vat_code',
                'came_from_id',
                'current_credits',
                'service_credits',
                'active',
                'allow',
                'archive',
                'visible',
                'last_login',
                'warning_sent',
                'blocked_until',
                'token',
                'created_at',
                'updated_at',
            ],
            self::SCENARIO_SYSTEM_MIGRATES_COMPANY_USER_DATA => [
                'id',
                'name',
                'surname',
                'email',
                'phone',
                'auth_key',
                'password_hash',
                'password_reset_token',
                'password_expires',
                'class',
                'original_class',
                'account_type',
                'personal_code',
                'company_code',
                'company_name',
                'city_id',
                'address',
                'vat_code',
                'came_from_id',
                'current_credits',
                'service_credits',
                'active',
                'allow',
                'archive',
                'visible',
                'last_login',
                'warning_sent',
                'blocked_until',
                'token',
                'created_at',
                'updated_at',
            ],
            self::SCENARIO_SYSTEM_MAKES_USER_AS_SUPPLIER => [
                'class',
            ],
            self::SCENARIO_SYSTEM_MIGRATES_USER => [
                'id',
                'name',
                'surname',
                'email',
                'phone',
                'auth_key',
                'password_hash',
                'password_reset_token',
                'password_expires',
                'class',
                'original_class',
                'account_type',
                'personal_code',
                'company_code',
                'company_name',
                'city_id',
                'address',
                'vat_code',
                'came_from_id',
                'current_credits',
                'service_credits',
                'active',
                'allow',
                'archive',
                'visible',
                'last_login',
                'warning_sent',
                'blocked_until',
                'token',
                'created_at',
                'updated_at',
                'suggestions',
                'suggestions_token',
            ],
            self::SCENARIO_SYSTEM_MAKES_CREDITBUYER => [
                'email',
                'name',
                'surname',
                'account_type',
                'company_code',
                'company_name',
                'came_from_referer',
                'cityIdNatural',
                'addressNatural',
                'vatCodeLegal',
                'rulesAgreement',
                'creditCodeService'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Name
            ['name', 'required', 'message' => Yii::t('app', 'USER_NAME_IS_REQUIRED')],
            ['name', 'filter', 'filter' => 'trim'],
            ['name', 'string', 'min' => self::NAME_MIN_LENGTH,
                          'tooShort' => Yii::t('app', 'USER_NAME_IS_TOO_SHORT', [
                              'length' => self::NAME_MIN_LENGTH,
                          ]),
                               'max' => self::NAME_MAX_LENGTH,
                           'tooLong' => Yii::t('app', 'USER_NAME_IS_TOO_LONG', [
                               'length' => self::NAME_MAX_LENGTH,
                           ]), 'when' => function () {
                                return !preg_match('/[А-Яа-яЁё]/u', $this->name);
                           }],
            ['name', 'validateName', 'params' => [
                'message' => Yii::t('app', 'USER_NAME_IS_NOT_MATCH'),
            ], 'except' => self::SCENARIO_SYSTEM_MIGRATES_COMPANY_OWNER_DATA],

            // Surname
            ['surname', 'required', 'message' => Yii::t('app', 'USER_SURNAME_IS_REQUIRED')],
            ['surname', 'filter', 'filter' => 'trim'],
            ['surname', 'string', 'min' => self::SURNAME_MIN_LENGTH,
                             'tooShort' => Yii::t('app', 'USER_SURNAME_IS_TOO_SHORT', [
                                 'length' => self::SURNAME_MIN_LENGTH,
                             ]),
                                  'max' => self::SURNAME_MAX_LENGTH,
                              'tooLong' => Yii::t('app', 'USER_SURNAME_IS_TOO_LONG', [
                                  'length' => self::SURNAME_MAX_LENGTH,
                              ]), 'when' => function () {
                                    return !preg_match('/[А-Яа-яЁё]/u', $this->surname);
                              }],
            ['surname', 'validateName', 'params' => [
                'message' => Yii::t('app', 'USER_SURNAME_IS_NOT_MATCH'),
            ], 'except' => self::SCENARIO_SYSTEM_MIGRATES_COMPANY_OWNER_DATA],

            // Email
            ['email', 'required', 'message' => Yii::t('app', 'USER_EMAIL_IS_REQUIRED')],
            ['email', 'email',
                'message' => Yii::t('app', 'USER_EMAIL_IS_NOT_EMAIL'),
                'except' => self::SCENARIO_SYSTEM_MIGRATES_USER],
            ['email', 'email', 'when' => function () {
                return !preg_match('/[А-Яа-яЁё]/u', $this->email);
            }, 'message' => Yii::t('app', 'USER_EMAIL_IS_NOT_EMAIL'),
                'on' => self::SCENARIO_SYSTEM_MIGRATES_USER],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'unique', 'targetClass' => '\common\models\User',
                                    'filter' => ['visible' => self::VISIBLE], // Because Creditcode user are invisible.
                                    'message' => Yii::t('app', 'USER_EMAIL_IS_NOT_UNIQUE', [
                                        'adminEmail' => Yii::$app->params['adminEmail']
                                    ]),
                                     'except' => [
                                         self::SCENARIO_USER_LOGINS,
                                         self::SCENARIO_USER_REQUESTS_PASSWORD_RESET,
                                         self::SCENARIO_EDIT_MY_DATA_CLIENT,
                                         self::SCENARIO_SYSTEM_MAKES_CREDITBUYER
                                     ]],
            ['email', 'string', 'max' => self::EMAIL_MAX_LENGTH,
                            'tooLong' => Yii::t('app', 'USER_EMAIL_IS_TOO_LONG', [
                                'length' => self::EMAIL_MAX_LENGTH,
                            ])],

            // Phone
            ['phone', 'required', 'message' => Yii::t('app', 'USER_PHONE_IS_REQUIRED'),'except' => [self::SCENARIO_SYSTEM_MAKES_CREDITBUYER]],
            ['phone', PhoneInputValidator::className(), 'message' => Yii::t('app', 'USER_PHONE_IS_NOT_MATCH'),
                'except' => [self::SCENARIO_SYSTEM_MIGRATES_USER, self::SCENARIO_SYSTEM_MAKES_CREDITBUYER]],

            // Auth key
            ['auth_key', 'required', 'message' => Yii::t('app', 'USER_AUTH_KEY_IS_REQUIRED')],
            ['auth_key', 'string', 'max' => self::AUTH_KEY_MAX_LENGTH,
                               'tooLong' => Yii::t('app', 'USER_AUTH_KEY_IS_TOO_LONG', [
                                   'length' => self::AUTH_KEY_MAX_LENGTH,
                               ])],

            // Password hash
            ['password_hash', 'required', 'message' => Yii::t('app', 'USER_PASSWORD_HASH_IS_REQUIRED'),
                                           'except' => self::SCENARIO_SYSTEM_UPDATES_COMPANY_USER],
            ['password_hash', 'string', 'message' => Yii::t('app', 'USER_PASSWORD_HASH_IS_NOT_STRING')],

            // Password reset token
            ['password_reset_token', 'string', 'message' => Yii::t('app', 'USER_PASSWORD_RESET_TOKEN_IS_NOT_STRING')],
            ['password_reset_token', 'unique', 'targetClass' => '\common\models\User',
                                                   'message' => Yii::t('app', 'USER_PASSWORD_RESET_TOKEN_IS_NOT_UNIQUE')],
            ['password_reset_token', 'default', 'value' => self::DEFAULT_PASSWORD_RESET_TOKEN],

            // Language
            ['language', 'required', 'message' => Yii::t('app', 'USER_LANGUAGE_IS_REQUIRED')],
            ['language', 'each', 'rule' => [
                 'exist', 'targetClass' => Language::className(),
                      'targetAttribute' => ['language' => 'id'],
                              'message' => Yii::t('app', 'USER_LANGUAGE_IS_NOT_IN_RANGE'),
            ]],

            // Password
            ['password', 'required', 'message' => Yii::t('app', 'USER_PASSWORD_IS_REQUIRED'),
                                      'except' => [self::SCENARIO_ADMIN_EDITS_COMPANY_USER, self::SCENARIO_SYSTEM_MAKES_CREDITBUYER]],
            ['password', 'string', 'min' => self::PASSWORD_MIN_LENGTH,
                              'tooShort' => Yii::t('app', 'USER_PASSWORD_IS_TOO_SHORT', [
                                  'length' => self::PASSWORD_MIN_LENGTH,
                              ]),
                                   'max' => self::PASSWORD_MAX_LENGTH,
                               'tooLong' => Yii::t('app', 'USER_PASSWORD_IS_TOO_LONG', [
                                   'length' => self::PASSWORD_MAX_LENGTH,
                               ])],

            // Repeat password
            ['repeatPassword', 'required', 'message' => Yii::t('app', 'USER_REPEAT_PASSWORD_IS_REQUIRED')],
            ['repeatPassword', 'compare', 'compareAttribute' => 'password',
                                                    'message' => Yii::t('app', 'USER_REPEAT_PASSWORD_IS_NOT_MATCH')],

            // Password expires
            ['password_expires', 'required', 'message' => Yii::t('app', 'USER_PASSWORD_EXPIRES_IS_REQUIRED')],
            ['password_expires', 'integer', 'message' => Yii::t('app', 'USER_PASSWORD_EXPIRES_IS_NOT_INTEGER')],

            // Current password
            ['currentPassword', 'required', 'message' => Yii::t('app', 'USER_CURRENT_PASSWORD_IS_REQUIRED')],

            // New password
            ['newPassword', 'required', 'message' => Yii::t('app', 'USER_NEW_PASSWORD_IS_REQUIRED')],
            ['newPassword', 'string', 'min' => self::PASSWORD_MIN_LENGTH,
                                 'tooShort' => Yii::t('app', 'USER_NEW_PASSWORD_IS_TOO_SHORT', [
                                     'length' => self::PASSWORD_MIN_LENGTH,
                                 ]),
                                      'max' => self::PASSWORD_MAX_LENGTH,
                                  'tooLong' => Yii::t('app', 'USER_NEW_PASSWORD_IS_TOO_LONG', [
                                      'length' => self::PASSWORD_MAX_LENGTH,
                                  ])],

            // Repeat new password
            ['repeatNewPassword', 'required', 'message' => Yii::t('app', 'USER_REPEAT_NEW_PASSWORD_IS_REQUIRED')],
            ['repeatNewPassword', 'compare', 'compareAttribute' => 'newPassword',
                                                      'message' => Yii::t('app', 'USER_REPEAT_NEW_PASSWORD_IS_NOT_MATCH')],

            // Class
            ['class', 'required', 'message' => Yii::t('app', 'USER_CLASS_IS_REQUIRED'),
                                   'except' => [
                                       self::SCENARIO_SIGN_UP_INVITATION_SERVER,
                                       self::SCENARIO_EXTENDED_CLIENT_SEARCH,
                                       self::SCENARIO_SYSTEM_MIGRATES_COMPANY_USER_DATA,
                                       self::SCENARIO_SYSTEM_MIGRATES_USER,
                                       self::SCENARIO_SYSTEM_MAKES_CREDITBUYER,
                                   ]],
            ['class', 'in', 'range' => [self::SUPPLIER, self::CARRIER, self::MINI_CARRIER],
                          'message' => Yii::t('app', 'USER_CLASS_IS_NOT_IN_RANGE')],
            ['class', 'default', 'value' => self::DEFAULT_CLASS,
                                    'on' => [
                                        self::SCENARIO_SIGN_UP_INVITATION_SERVER,
                                        self::SCENARIO_EXTENDED_CLIENT_SEARCH,
                                        self::SCENARIO_SYSTEM_MIGRATES_USER,
                                    ]],

            // Original class
            ['original_class', 'required', 'message' => Yii::t('app', 'USER_ORIGINAL_CLASS_IS_REQUIRED'),
                                            'except' => [
                                                self::SCENARIO_SIGN_UP_INVITATION_SERVER,
                                                self::SCENARIO_SYSTEM_MIGRATES_COMPANY_USER_DATA,
                                                self::SCENARIO_SYSTEM_MIGRATES_USER,
                                                self::SCENARIO_SYSTEM_MAKES_CREDITBUYER,
                                            ]],
            ['original_class', 'in', 'range' => [self::SUPPLIER, self::CARRIER, self::MINI_CARRIER],
                                   'message' => Yii::t('app', 'USER_ORIGINAL_CLASS_NOT_IN_RANGE')],
            ['original_class', 'default', 'value' => self::DEFAULT_ORIGINAL_CLASS,
                                             'on' => [
                                                 self::SCENARIO_SIGN_UP_INVITATION_SERVER,
                                                 self::SCENARIO_SYSTEM_MIGRATES_USER,
                                             ]],

            // Account type
            ['account_type', 'required', 'message' => Yii::t('app', 'USER_ACCOUNT_TYPE_IS_REQUIRED'),
                                          'except' => [
                                              self::SCENARIO_SIGN_UP_INVITATION_SERVER,
                                              self::SCENARIO_SYSTEM_MIGRATES_COMPANY_USER_DATA,
                                              self::SCENARIO_SYSTEM_MIGRATES_USER,
                                          ]],
            ['account_type', 'in', 'range' => [self::NATURAL, self::LEGAL],
                                 'message' => Yii::t('app', 'USER_ACCOUNT_TYPE_IS_NOT_IN_RANGE')],
            ['account_type', 'default', 'value' => self::DEFAULT_ACCOUNT_TYPE,
                                           'on' => [
                                               self::SCENARIO_SIGN_UP_INVITATION_SERVER,
                                               self::SCENARIO_SYSTEM_MIGRATES_USER,
                                           ]],

            // Personal code
            ['personal_code', 'filter', 'filter' => 'trim'],
            ['personal_code', 'default', 'value' => self::DEFAULT_PERSONAL_CODE],
            ['personal_code', 'match', 'pattern' => '/^[\-\.\/\s\w]{5,18}$/',
                                       'message' => Yii::t('app', 'USER_PERSONAL_CODE_IS_NOT_MATCH')],
                    
            // came_from_referer
            ['came_from_referer', 'string', 'on' => [self::SCENARIO_SIGN_UP_INVITATION_SERVER, self::SCENARIO_SIGN_UP_SERVER]],

            // Company code
            ['company_code', 'required', 'when' => function (self $model) {
                                                       return $model->account_type == self::LEGAL;
                                                   },
                                   'whenClient' => "function (attribute, value) { " .
                                       "return $('#RG-F-18').val() == '" . self::LEGAL . "'; " .
                                   "}",
                                      'message' => Yii::t('app', 'USER_COMPANY_CODE_IS_REQUIRED'),
                                       'except' => self::SCENARIO_SYSTEM_MIGRATES_USER],
            ['company_code', 'unique', 'targetClass' => '\common\models\User',
                                        'filter' => ['visible' => self::VISIBLE], // Because Creditcode user are invisible.
                                        'message' => Yii::t('app', 'USER_COMPANY_CODE_IS_NOT_UNIQUE', [
                                            'userEmail' => self::getEmailByCompanyCode($this->company_code),
                                            'adminEmail' => Yii::$app->params['adminEmail']
                                        ]),
                                        'except' => [
                                                self::SCENARIO_SYSTEM_MIGRATES_USER,
                                                self::SCENARIO_SYSTEM_MAKES_CREDITBUYER
                                        ]],
            ['company_code', 'default', 'value' => self::DEFAULT_COMPANY_CODE],

            // Company name
            ['company_name', 'required', 'when' => function (self $model) {
                                                       return $model->account_type == self::LEGAL;
                                                   },
                                   'whenClient' => "function (attribute, value) { " .
                                       "return $('#RG-F-18').val() == '" . self::LEGAL . "'; " .
                                   "}",
                                      'message' => Yii::t('app', 'USER_COMPANY_NAME_IS_REQUIRED'),
                                       'except' => [
                                           self::SCENARIO_SYSTEM_MIGRATES_USER
                                        ]],
            ['company_name', 'unique', 'targetClass' => '\common\models\User',
                                            'filter' => ['visible' => self::VISIBLE], // Because Creditcode user are invisible.
                                           'message' => Yii::t('app', 'USER_COMPANY_NAME_IS_NOT_UNIQUE', [
                                               'userEmail' => self::getEmailByCompanyName($this->company_name),
                                               'adminEmail' => Yii::$app->params['adminEmail']
                                           ]),
                                           'except' => [
                                                self::SCENARIO_SYSTEM_MIGRATES_USER,
                                                self::SCENARIO_SYSTEM_MAKES_CREDITBUYER
                                            ]],
            ['company_name', 'default', 'value' => self::DEFAULT_COMPANY_NAME],

            // City ID natural
            ['cityIdNatural', 'required', 'when' => function (self $model) {
                                                        return $model->account_type == self::NATURAL;
                                                    },
                                    'whenClient' => "function (attribute, value) { " .
                                        "return $('#RG-F-18').val() == '" . self::NATURAL . "'; " .
                                    "}",
                                       'message' => Yii::t('app', 'USER_CITY_ID_NATURAL_IS_REQUIRED')],

            // City ID legal
            ['cityIdLegal', 'required', 'when' => function (self $model) {
                                                      return $model->account_type == self::LEGAL;
                                                  },
                                  'whenClient' => "function (attribute, value) { " .
                                      "return $('#RG-F-18').val() == '" . self::LEGAL . "'; " .
                                  "}",
                                     'message' => Yii::t('app', 'USER_CITY_ID_LEGAL_IS_REQUIRED')],

            // Address natural
            ['addressNatural', 'required', 'when' => function (self $model) {
                                                         return $model->account_type == self::NATURAL;
                                                     },
                                     'whenClient' => "function (attribute, value) { " .
                                         "return $('#RG-F-18').val() == '" . self::NATURAL . "'; " .
                                     "}",
                                        'message' => Yii::t('app', 'USER_ADDRESS_NATURAL_IS_REQUIRED')],

            // Address legal
            ['addressLegal', 'required', 'when' => function (self $model) {
                                                       return $model->account_type == self::LEGAL;
                                                   },
                                   'whenClient' => "function (attribute, value) { " .
                                       "return $('#RG-F-18').val() == '" . self::LEGAL . "'; " .
                                   "}",
                                      'message' => Yii::t('app', 'USER_ADDRESS_LEGAL_IS_REQUIRED')
                                    ],

            // VAT code natural
            ['vatCodeNatural', 'string', 'message' => Yii::t('app', 'USER_VAT_CODE_NATURAL_IS_NOT_STRING')],
            ['vatCodeNatural', 'validateVatCode', 'when' => function (self $model) {
                                                                return $model->account_type == self::NATURAL;
                                                            },
                                                  'whenClient' => "function (attribute, value) { " .
                                                      "return $('#RG-F-18').val() == '" . self::NATURAL . "'; " .
                                                  "}"],

            // VAT code legal
            ['vatCodeLegal', 'string', 'message' => Yii::t('app', 'USER_VAT_CODE_LEGAL_IS_NOT_STRING')],
            ['vatCodeLegal', 'validateVatCode', 'when' => function (self $model) {
                                                              return $model->account_type == self::LEGAL;
                                                          },
                                                'whenClient' => "function (attribute, value) { " .
                                                    "return $('#RG-F-18').val() == '" . self::LEGAL . "'; " .
                                                "}"],

            // City ID
            ['city_id', 'required', 'message' => Yii::t('app', 'USER_CITY_ID_IS_REQUIRED'),
                                     'except' => [
                                         self::SCENARIO_SIGN_UP_INVITATION_SERVER,
                                         self::SCENARIO_SYSTEM_MIGRATES_COMPANY_USER_DATA,
                                         self::SCENARIO_SYSTEM_MIGRATES_USER,
                                     ]],
            ['city_id', 'exist', 'targetClass' => City::className(),
                             'targetAttribute' => ['city_id' => 'id']],
            ['city_id', 'default', 'value' => self::DEFAULT_CITY_ID,
                                      'on' => [
                                          self::SCENARIO_SIGN_UP_INVITATION_SERVER,
                                          self::SCENARIO_SYSTEM_MIGRATES_USER,
                                      ]],

            // Address
            ['address', 'required', 'message' => Yii::t('app', 'USER_ADDRESS_IS_REQUIRED'),
                                     'except' => [
                                         self::SCENARIO_SIGN_UP_INVITATION_SERVER,
                                         self::SCENARIO_SYSTEM_MIGRATES_COMPANY_USER_DATA,
                                         self::SCENARIO_SYSTEM_MIGRATES_COMPANY_OWNER_DATA,
                                         self::SCENARIO_SYSTEM_MIGRATES_USER,
                                     ]],
            ['address', 'string', 'max' => self::ADDRESS_MAX_LENGTH,
                              'tooLong' => Yii::t('app', 'USER_ADDRESS_TOO_LONG', [
                                  'length' => self::ADDRESS_MAX_LENGTH,
                              ])],
            ['address', 'default', 'value' => self::DEFAULT_ADDRESS,
                                      'on' => [
                                          self::SCENARIO_SIGN_UP_INVITATION_SERVER,
                                          self::SCENARIO_SYSTEM_MIGRATES_USER,
                                      ]],

            // VAT code
            ['vat_code', 'validateVatCode', 'except' => self::SCENARIO_SYSTEM_MIGRATES_USER],
            ['vat_code', 'default', 'value' => self::DEFAULT_VAT_CODE,
                                       'on' => [
                                           self::SCENARIO_SIGN_UP_INVITATION_SERVER,
                                           self::SCENARIO_SYSTEM_MIGRATES_USER,
                                       ]],

            // Came from id
			['came_from_id', 'required', 'message' => Yii::t('app', 'USER_CHOICE_IS_REQUIRED'),
                'on' => [self::SCENARIO_SIGN_UP_CLIENT, self::SCENARIO_SIGN_UP_SERVER, self::SCENARIO_SYSTEM_MAKES_CREDITBUYER]],
            ['came_from_id', 'exist', 'targetClass' => CameFrom::className(),
                                  'targetAttribute' => ['came_from_id' => 'id'],
                                          'message' => Yii::t('app', 'USER_CAME_FROM_ID_NOT_IN_RANGE')],
            ['came_from_id', 'default', 'value' => self::DEFAULT_CAME_FROM_ID,
                                           'on' => [
                                               self::SCENARIO_SIGN_UP_INVITATION_SERVER,
                                               self::SCENARIO_SYSTEM_MIGRATES_USER,
                                           ]],
            ['service_credits', 'integer'],
            // Current credits
            ['current_credits', 'default', 'value' => self::DEFAULT_CURRENT_CREDITS],
            ['current_credits', 'integer', 'min' => self::CURRENT_CREDITS_MIN_VALUE,
                                      'tooSmall' => Yii::t('app', 'USER_CURRENT_CREDITS_IS_TOO_SMALL', [
                                          'min' => self::CURRENT_CREDITS_MIN_VALUE,
                                      ]),
                                       'message' => Yii::t('app', 'USER_CURRENT_CREDITS_IS_NOT_INTEGER')],

            // Active
            ['active', 'default', 'value' => self::ACTIVE],
            ['active', 'in', 'range' => [self::INACTIVE, self::ACTIVE],
                           'message' => Yii::t('app', 'USER_ACTIVE_IS_NOT_IN_RANGE'),
                            'except' => self::SCENARIO_ADMIN_ADDS_NEW_COMPANY_USER],

            // Allow
            ['allow', 'default', 'value' => self::FORBIDDEN],
            ['allow', 'in', 'range' => [self::FORBIDDEN, self::ALLOWED],
                          'message' => Yii::t('app', 'USER_ALLOW_IS_NOT_IN_RANGE')],

            // Archive
            ['archive', 'default', 'value' => self::NOT_ARCHIVED],
            ['archive', 'in', 'range' => array_keys(self::getTranslatedArchives()),
                            'message' => Yii::t('app', 'USER_ARCHIVE_IS_NOT_IN_RANGE')],

            // Visible
            ['visible', 'default', 'value' => self::INVISIBLE],
            ['visible', 'in', 'range' => [self::INVISIBLE, self::VISIBLE],
                            'message' => Yii::t('app', 'USER_VISIBLE_IS_NOT_IN_RANGE')],
                            
            // Suggestions
            ['suggestions', 'default', 'value' => self::SEND_SUGGESTIONS],
            ['suggestions', 'in', 'range' => [self::SEND_SUGGESTIONS, self::DO_NOT_SEND_SUGGESTIONS],
                            'message' => Yii::t('app', 'SEND_SUGGESTIONS_IS_NOT_IN_RANGE')],
                            
            // Suggestions_token
            ['suggestions_token', 'string', 'message' => Yii::t('app', 'SUGGESTIONS_TOKEN_IS_NOT_STRING')],

            // Last login
            ['last_login', 'default', 'value' => time()],
            ['last_login', 'integer', 'message' => Yii::t('app', 'USER_LAST_LOGIN_IS_NOT_INTEGER'),
                            'except' => self::SCENARIO_EXTENDED_CLIENT_SEARCH],
                            
            // Start last login
            ['start_last_login', 'integer', 'message' => Yii::t('app', 'USER_LAST_LOGIN_IS_NOT_INTEGER'),
                            'except' => self::SCENARIO_EXTENDED_CLIENT_SEARCH],
                            
            // End last login
            ['end_last_login', 'integer', 'message' => Yii::t('app', 'USER_LAST_LOGIN_IS_NOT_INTEGER'),
                            'except' => self::SCENARIO_EXTENDED_CLIENT_SEARCH],
                            
            // Start range last login
            ['start_created_at', 'integer', 'message' => Yii::t('app', 'USER_LAST_LOGIN_IS_NOT_INTEGER'),
                            'except' => self::SCENARIO_EXTENDED_CLIENT_SEARCH],
                            
            // End range created at
            ['end_created_at', 'integer', 'message' => Yii::t('app', 'USER_LAST_LOGIN_IS_NOT_INTEGER'),
                            'except' => self::SCENARIO_EXTENDED_CLIENT_SEARCH],

            // Warning sent
            ['warning_sent', 'default', 'value' => self::DEFAULT_WARNING_SENT],
            ['warning_sent', 'integer', 'message' => Yii::t('app', 'USER_WARNING_SENT_IS_NOT_INTEGER')],

            // Blocked until
            ['blocked_until', 'default', 'value' => self::DEFAULT_BLOCKED_UNTIL],
            ['blocked_until', 'integer', 'message' => Yii::t('app', 'USER_BLOCKED_UNTIL_IS_NOT_INTEGER'),
                                          'except' => self::SCENARIO_ADMIN_EDITS_COMPANY_USER],
            ['blocked_until', 'date', 'format' => 'php:Y-m-d',
                                  'message' => Yii::t('app', 'USER_BLOCKED_UNTIL_INVALID_FORMAT', [
                                      'example' => date('Y-m-d'),
                                  ]),
                                       'on' => self::SCENARIO_ADMIN_EDITS_COMPANY_USER],

            // Token
            ['token', 'string', 'max' => self::TOKEN_LENGTH,
                            'tooLong' => Yii::t('app', 'USER_TOKEN_IS_TOO_LONG', [
                                'length' => self::TOKEN_LENGTH,
                            ])],
            ['token', 'unique', 'targetClass' => '\common\models\User',
                                    'message' => Yii::t('app', 'USER_TOKEN_IS_NOT_UNIQUE')],
            ['token', 'default', 'value' => self::DEFAULT_TOKEN],

            // Created at
            ['created_at', 'integer', 'message' => Yii::t('app', 'USER_CREATED_AT_IS_NOT_INTEGER')],

            // Updated at
            ['updated_at', 'integer', 'message' => Yii::t('app', 'USER_UPDATED_AT_IS_NOT_INTEGER')],

            // Rules agreement
            ['rulesAgreement', 'required', 'requiredValue' => self::AGREE_WITH_RULES,
                                                 'message' => Yii::t('app', 'USER_RULES_AGREEMENT_IS_REQUIRED', [
                                                     'rules' => Yii::t('element', 'RG-F-22a')
                                                 ])],

            // Change email
            ['changeEmail', 'required', 'message' => Yii::t('app', 'USER_CHANGE_EMAIL_IS_REQUIRED')],
            ['changeEmail', 'filter', 'filter' => 'trim'],
            ['changeEmail', 'string', 'min' => self::CHANGE_EMAIL_TEXT_MIN_LENGTH,
                                 'tooShort' => Yii::t('app', 'USER_CHANGE_EMAIL_IS_TOO_SHORT', [
                                     'length' => self::CHANGE_EMAIL_TEXT_MIN_LENGTH,
                                 ]),
                                      'max' => self::CHANGE_EMAIL_TEXT_MAX_LENGTH,
                                  'tooLong' => Yii::t('app', 'USER_CHANGE_EMAIL_IS_TOO_LONG', [
                                      'length' => self::CHANGE_EMAIL_TEXT_MAX_LENGTH,
                                  ])],

            // Send email
            ['sendEmail', 'default', 'value' => self::SEND_EMAIL],
            ['sendEmail', 'in', 'range' => [self::NOT_SENT_EMAIL, self::SEND_EMAIL],
                              'message' => Yii::t('app', 'USER_SEND_EMAIL_NOT_IN_RANGE'),
                               'except' => self::SCENARIO_ADMIN_ADDS_NEW_COMPANY_USER],
            ['creditCodeService', 'required', 
                'message' => Yii::t('app', 'CREDITCODE_SERVICE_NOT_SELECTED'),
                'on' => [self::SCENARIO_SYSTEM_MAKES_CREDITBUYER]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'USER_LABEL_ID'),
            'name' => Yii::t('app', 'USER_LABEL_NAME'),
            'surname' => Yii::t('app', 'USER_LABEL_SURNAME'),
            'email' => Yii::t('app', 'USER_LABEL_EMAIL'),
            'phone' => Yii::t('app', 'USER_LABEL_PHONE'),
            'language' => Yii::t('app', 'USER_LABEL_LANGUAGE'),
            'came_from_referer' => 'Came from',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'password_expires' => Yii::t('app', 'USER_LABEL_PASSWORD_EXPIRES'),
            'class' => Yii::t('app', 'USER_LABEL_CLASS'),
            'account_type' => Yii::t('app', 'USER_LABEL_ACCOUNT_TYPE'),
            'personal_code' => Yii::t('app', 'USER_LABEL_PERSONAL_CODE'),
            'company_code' => Yii::t('app', 'USER_LABEL_COMPANY_CODE'),
            'company_name' => Yii::t('app', 'USER_LABEL_COMPANY_NAME'),
            'cityIdNatural' => Yii::t('app', 'USER_LABEL_CITY_ID_NATURAL'),
            'cityIdLegal' => Yii::t('app', 'USER_LABEL_CITY_ID_LEGAL'),
            'addressNatural' => Yii::t('app', 'USER_LABEL_ADDRESS_NATURAL'),
            'addressLegal' => Yii::t('app', 'USER_LABEL_ADDRESS_LEGAL'),
            'vatCodeNatural' => Yii::t('app', 'USER_LABEL_VAT_CODE_NATURAL'),
            'vatCodeLegal' => Yii::t('app', 'USER_LABEL_VAT_CODE_LEGAL'),
            'city_id' => Yii::t('app', 'USER_LABEL_CITY_ID'),
            'address' => Yii::t('app', 'USER_LABEL_ADDRESS'),
            'vat_code' => Yii::t('app', 'USER_LABEL_VAT_CODE'),
            'came_from_id' => Yii::t('app', 'USER_LABEL_CAME_FROM_ID'),
            'current_credits' => Yii::t('app', 'USER_LABEL_CURRENT_CREDITS'),
            'service_credits' => Yii::t('app', 'USER_LABEL_SERVICE_CREDITS'),
            'active' => Yii::t('app', 'USER_LABEL_ACTIVE'),
            'allow' => Yii::t('app', 'USER_LABEL_ALLOW'),
            'archive' => Yii::t('app', 'USER_LABEL_ARCHIVE'),
            'visible' => Yii::t('app', 'USER_LABEL_VISIBLE'),
            'last_login' => Yii::t('app', 'USER_LABEL_LAST_LOGIN'),
            'warning_sent' => Yii::t('app', 'USER_LABEL_WARNING_SENT'),
            'blocked_until' => Yii::t('app', 'USER_LABEL_BLOCKED_UNIT'),
            'token' => Yii::t('app', 'USER_LABEL_TOKEN'),
            'created_at' => Yii::t('app', 'USER_LABEL_CREATED_AT'),
            'updated_at' => Yii::t('app', 'USER_LABEL_UPDATED_AT'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAdminAsUsers()
    {
        return $this->hasMany(AdminAsUser::className(), ['user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCompanies()
    {
        return $this->hasMany(Company::className(), ['owner_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCompanyUser()
    {
        return $this->hasOne(CompanyUser::className(), ['user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLoads()
    {
        return $this->hasMany(Load::className(), ['user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLoadPreviews()
    {
        return $this->hasMany(LoadPreview::className(), ['user_id' => 'id']);
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
    public function getCameFrom()
    {
        return $this->hasOne(CameFrom::className(), ['id' => 'came_from_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUserLanguages()
    {
        return $this->hasMany(UserLanguage::className(), ['user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUserLogs()
    {
        return $this->hasMany(UserLog::className(), ['user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUserServices()
    {
        return $this->hasMany(UserService::className(), ['user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUserServiceActives()
    {
        return $this->hasMany(UserServiceActive::className(), ['user_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }
    
    /**
     * @return ActiveQuery
     */
    public function getUserServiceActive()
    {
        return $this->hasMany(UserServiceActive::className(), ['user_id' => 'id']);
    }
    
    /**
     * @return ActiveQuery
     */
    public function getUserService()
    {
        return $this->hasMany(UserService::className(), ['user_id' => 'id']);
    }
    
    /**
     * @return ActiveQuery
     */
    public function getOwnerService()
    {
        return $this->hasMany(UserService::className(), ['user_id' => 'id'])
                ->from(UserService::tableName() . ' ownerService');
    }
    
    /**
     * @return ActiveQuery
     */
    public function getLoad()
    {
        return $this->hasMany(Load::className(), ['user_id' => 'id']);
    }
    
    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    /**
     * Loads user model by provided user ID
     *
     * @param null|integer $id User ID that model needs to be loaded
     * @return null|static
     * @throws NotFoundHttpException If user model not found
     */
    public static function findById($id = null)
    {
        $user = self::findOne($id);
        if (is_null($user)) {
            throw new NotFoundHttpException(Yii::t('alert', 'NOT_FOUND_USER_BY_ID'));
        }
        $user->language = UserLanguage::getUserLanguages($id);
        return $user;
    }

    /**
     * Finds user by email
     *
     * @param string $email User email that model must be found
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token Password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne(['password_reset_token' => $token]);
    }

    /**
     * Checks whether password reset token is valid
     *
     * @param string $token Password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * Finds user by token
     *
     * @param string $token User token
     * @return null|static
     */
    public static function findByToken($token)
    {
        return static::findOne(['token' => $token, 'allow' => self::FORBIDDEN, 'visible' => self::INVISIBLE]);
    }

    /**
     * Validates VAT code
     *
     * @param string $attribute VAT code attribute name that is currently being validated
     */
    public function validateVatCode($attribute)
    {
        if (is_null(self::isVatCodeLengthValid($this->$attribute))) {
            return;
        }
        if (!preg_match('/^[A-Z]{2}[0-9]{2,15}$/', $this->$attribute)) {
            $this->addError($attribute, Yii::t('app', 'VALIDATE_VAT_CODE_NUMBER_IS_NOT_MATCH'));
            return;
        }
        list($code, $number) = self::splitVatCode($this->$attribute, self::VAT_CODE_MIN_LENGTH);
        $countries = Country::getVatRateCountries();
        if (!array_key_exists($code, $countries)) {
            $this->addError($attribute, Yii::t('app', 'VALIDATE_VAT_CODE_INVALID_COUNTRY_CODE'));
        }
        if (!self::isVatCodeValidByEC($code, $number)) {
            $this->addError($attribute, Yii::t('app', 'VALIDATE_VAT_CODE_NOT_IN_EUROPEAN_COMMISSION'));
        }
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
     * Checks whether VAT code is registered in European Commission
     *
     * @param string $code Country code (two digital letters of country name)
     * @param integer $number VAT code number without country code
     * @return boolean
     */
    public static function isVatCodeValidByEC($code, $number)
    {
        $response = self::getInfoFromECByVatCode($code, $number);
        return $response['valid'];
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
     * Validates user name or surname
     *
     * @param string $attribute Attribute name that is being validated
     * @param mixed $params The value of the "params" given in the rule
     */
    public function validateName($attribute, $params)
    {
        $name = trim($this->$attribute);
        if (!preg_match('/^((\b[a-zA-Z\p{L}\-]{1,}\b)\s*){1,}$/u', $name)) {
            $this->addError($attribute, $params['message']);
        }
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password Password that needs to be validated
     * @return boolean
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Returns list of all translated classes
     *
     * @return array
     */
    public static function getClasses()
    {
        $carrierIcon = Html::img(Yii::getAlias('@web') . '/images/long_trailer_icon.png');
        $miniCarrierIcon = Html::img(Yii::getAlias('@web') . '/images/trailer_icon.png');
        $supplier = Html::img(Yii::getAlias('@web') . '/images/car_icon.png');

        return [
            self::CARRIER => Html::tag('div', Yii::t('app', 'CLASS_LABEL_CARRIER') . Html::tag('div', $carrierIcon, ['class' => 'select2__icon']), ['class' => 'select2__icon-text']),
            self::MINI_CARRIER => Html::tag('div', Yii::t('app', 'CLASS_LABEL_MINI_CARRIER') . Html::tag('div', $miniCarrierIcon, ['class' => 'select2__icon']), ['class' => 'select2__icon-text']),
            self::SUPPLIER => Html::tag('div', Yii::t('app', 'CLASS_LABEL_SUPPLIER') . Html::tag('div', $supplier, ['class' => 'select2__icon']), ['class' => 'select2__icon-text']),
        ];
    }

    /**
     * Returns list of all translated account types
     *
     * @return array
     */
    public static function getAccountTypes()
    {
        return [
            self::NATURAL => Yii::t('app', 'ACCOUNT_TYPE_LABEL_NATURAL'),
            self::LEGAL => Yii::t('app', 'ACCOUNT_TYPE_LABEL_LEGAL'),
        ];
    }

    /**
     * Creates new user account
     *
     * @return boolean Whether user account were created successfully
     */
    public function create()
    {
        $this->setPassword($this->password);
        $this->generateAuthKey();
        $this->setPasswordExpiration();
        $this->setAttributesByAccountType();
        $this->setToken();
        $this->setSupplierClass();
        $this->suggestions_token = Yii::$app->security->generateRandomString();
        $this->setCameFromReferer();
        $this->scenario = self::SCENARIO_SIGN_UP_SERVER;
        $transaction = Yii::$app->db->beginTransaction();

        if (!$this->save() || !UserLanguage::create($this->id, $this->language) || !$this->sendSignUpConfirmation()) {
            $transaction->rollBack();
            return false;
        }

        $transaction->commit();

        $isSent = $this->original_class == self::CARRIER ? $this->sendCarrierDocumentsRequest() : false;
        if ($isSent) {
            $systemMessagePlaceholder = SystemMessage::PLACEHOLDER_USER_RECEIVED_CARRIER_DOCUMENTS_REQUEST;
            Log::user(SystemMessage::ACTION, $systemMessagePlaceholder, [], $this->id);
        }

        return true;
    }

        /**
     * Creates new user account
     *
     * @return boolean Whether user account were created successfully
     */
    public function createCreditCodesBuyer($lang)
    {
        $this->setPassword("");
        $this->phone = "";
        $this->personal_code = self::DEFAULT_PERSONAL_CODE;
        $this->city_id = $this->cityIdNatural;
        $this->address = $this->addressNatural;
        $this->vat_code = self::isVatCodeLengthValid($this->vatCodeNatural);
        if ($this->account_type == User::NATURAL) {
            $this->company_code = null;
            $this->company_name = null;
        }
        $this->setSupplierClass();
        $this->setCameFromReferer();
        $this->scenario = self::SCENARIO_SYSTEM_MAKES_CREDITBUYER;
        $transaction = Yii::$app->db->beginTransaction();
        if (!$this->save()) {
            $transaction->rollBack();
            return false;
        }
        $transaction->commit();

        return true;
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Sets the timestamp when user password must be expired
     *
     * @param string $expiresAfter After what time user password will be expired
     */
    public function setPasswordExpiration($expiresAfter = '+1 year')
    {
        $this->password_expires = strtotime($expiresAfter, time());
    }

    /**
     * Sets corresponding attributes by selected account type
     *
     * @throws NotAcceptableHttpException If account type is neither NATURAL nor LEGAL
     */
    private function setAttributesByAccountType()
    {
        $this->personal_code = self::DEFAULT_PERSONAL_CODE;
        switch ($this->account_type) {
            case User::NATURAL:
                $this->company_code = null;
                $this->company_name = null;
                $this->city_id = $this->cityIdNatural;
                $this->address = $this->addressNatural;
                $this->vat_code = self::isVatCodeLengthValid($this->vatCodeNatural);
                break;
            case User::LEGAL:
                $this->city_id = $this->cityIdLegal;
                $this->address = $this->addressLegal;
                $this->vat_code = self::isVatCodeLengthValid($this->vatCodeLegal);
                break;
            default:
                throw new NotAcceptableHttpException(Yii::t('alert', 'INVALID_USER_ACCOUNT_TYPE'));
        }
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
     * Generates new token and sets it to model attribute
     *
     * @param integer $length Number of characters that token must contain
     */
    private function setToken($length = self::TOKEN_LENGTH)
    {
        $this->token = Yii::$app->security->generateRandomString($length);
    }
    
    /**
     * Sets page from witch user came saved in session
     */
    private function setCameFromReferer()
    {
        if (Yii::$app->session->has('register_referer')) {
            $this->came_from_referer = Yii::$app->session['register_referer'];
        }
    }

    /**
     * Sets user class as supplier, but also remembers which class was selected by user
     */
    private function setSupplierClass()
    {
        $this->original_class = $this->class;
        if ($this->class == self::CARRIER) {
            $this->class = self::SUPPLIER;
        }
    }

    /**
     * Sends sign up confirmation link to currently signed up user email
     *
     * @return boolean Whether mail was sent successfully
     */
    public function sendSignUpConfirmation()
    {
        $isSent = Yii::$app->mailer->compose('user/sign-up-confirmation', [
            'url' => $this->getSignUpConfirmationUrl(),
            'companyName' => Yii::$app->params['companyName'],
        ])
            ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->params['companyName']])
            ->setTo($this->email)
            ->setSubject(Yii::t('mail', 'USER_SIGN_UP_CONFIRMATION_SUBJECT', [
                'companyName' => Yii::$app->params['companyName'],
            ]))
            ->send();

        if ($isSent) {
            Log::user(SystemMessage::ACTION, SystemMessage::PLACEHOLDER_USER_RECEIVED_SIGN_UP_CONFIRMATION, [], $this->id);
        }

        return $isSent;
    }

    /**
     * Returns generated sign up confirmation link
     *
     * @return string
     */
    private function getSignUpConfirmationUrl()
    {
        return Url::to([
            'site/confirm-sign-up',
            'lang' => Yii::$app->language,
            'token' => $this->token,
        ], true);
    }

    /**
     * Sends request for carrier documents to currently signed up user email
     *
     * @return boolean
     */
    public function sendCarrierDocumentsRequest()
    {
        return Yii::$app->mailer->compose('user/carrier-documents-request', [
                                    'companyName' => Yii::$app->params['companyName'],
                                ])
                                ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->params['companyName']])
                                ->setTo($this->email)
                                ->setSubject(Yii::t('mail', 'USER_CARRIER_DOCUMENTS_REQUEST_SUBJECT'))
                                ->send();
    }

    /**
     * Returns user email by given company code
     *
     * @param string $companyCode Company code that already exists in database
     * @return false|null|string
     */
    public static function getEmailByCompanyCode($companyCode)
    {
        return self::find()->select('email')->where(['company_code' => $companyCode])->scalar();
    }

    /**
     * Returns user email by given company name
     *
     * @param string $companyName Company name that already exists in database
     * @return false|null|string
     */
    public static function getEmailByCompanyName($companyName)
    {
        return self::find()->select('email')->where(['company_name' => $companyName])->scalar();
    }

    /**
     * Returns company owner email by provided company title
     *
     * @param string $title Company title
     * @return false|null|string
     */
    public static function getEmailByTitle($title)
    {
        return self::find()
                   ->select(self::tableName() . '.email')
                   ->innerJoin(Company::tableName(), Company::tableName() . '.owner_id = ' . self::tableName() . '.id')
                   ->where(Company::tableName() . ".title LIKE '" . Html::encode($title) . "'") // FIXME
                   ->scalar();
    }

    /**
     * Confirms user sign up
     *
     * @return boolean Whether user sign up were confirmed successfully
     */
    public function confirmSignUp()
    {
        $this->removeToken();
        $this->allowLogin();
        $this->makeVisible();
        return $this->save();
    }

    /**
     * Removes user token
     */
    private function removeToken()
    {
        $this->token = null;
    }

    /**
     * Allows user to login
     */
    private function allowLogin()
    {
        $this->allow = self::ALLOWED;
    }

    /**
     * Makes user visible to everyone
     */
    private function makeVisible()
    {
        $this->visible = self::VISIBLE;
    }

    /**
     * Checks whether user is allowed to login
     *
     * @return boolean
     */
    public function isAllowed()
    {
        return $this->allow == self::ALLOWED;
    }

    /**
     * Checks whether user is forbidden to login
     *
     * @return boolean
     */
    public function isForbidden()
    {
        return $this->allow == self::FORBIDDEN;
    }

    /**
     * Generates password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Returns newly generated password reset request URL
     *
     * @return string
     */
    private function getPasswordResetRequestUrl()
    {
        return Url::to([
            'site/reset-password',
            'lang'  => Yii::$app->language,
            'token' => $this->password_reset_token,
        ], true);
    }

    /**
     * Sends password reset link to users email
     *
     * @return boolean Whether mail was sent successfully
     */
    public function sendPasswordReset()
    {
        return Yii::$app->mailer->compose('user/password-reset-request', [
            'url' => $this->getPasswordResetRequestUrl(),
            'companyName' => Yii::$app->params['companyName'],
        ])
            ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->params['companyName']])
            ->setTo($this->email)
            ->setSubject(Yii::t('mail', 'USER_PASSWORD_RESET_REQUEST_SUBJECT'))
            ->send();
    }
    
    /**
     * Sends newest suggestions link to users email
     *
     * @return boolean Whether mail was sent successfully
     */
    public function sendSuggestionsLink($token = '', $rejectToken = '', $userEmail = '')
    {
        return Yii::$app->mailer->compose('load/load-suggestions', [
            'url' => $this->getLoadSuggestionsUrl($token),
            'rejectUrl' => $this->getRejectLoadSuggestionsUrl($rejectToken, $userEmail),
            'companyName' => Yii::$app->params['companyName'],
        ])
            ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->params['companyName']])
            ->setTo($this->email) //TODO pakeisti live pasta i $this->email
            ->setSubject(Yii::t('mail', 'LOAD_SUGGESTIONS_SUBJECT'))
            ->send();
    }
    
    /**
     * Creates link to newest suggestion page
     *
     * @param string $token idetinfication token to access loads
     * @return string
     */
    private function getRejectLoadSuggestionsUrl($rejectToken, $userEmail)
    {
        return Url::to([
            'load/reject-newest-suggestions',
            'lang' => Yii::$app->language,
            'email' => $userEmail,
            'token' => $rejectToken,
        ], true);
    }
    
    /**
     * Creates link to newest suggestion page
     *
     * @param string $token idetinfication token to access loads
     * @return string
     */
    private function getLoadSuggestionsUrl($token)
    {
        return Url::to([
            'load/newest-suggestions',
            'lang' => Yii::$app->language,
            'token' => $token,
        ], true);
    }

    /**
     * Resets password
     *
     * @return boolean|integer Number of rows affected, or false if validation fails
     */
    public function resetPassword()
    {
        $this->scenario = self::SCENARIO_RESET_PASSWORD_SERVER;
        $this->setPassword($this->password);
        $this->setPasswordExpiration();
        $this->removePasswordResetToken();
        return $this->update();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Requests administrator for user email change
     *
     * @return boolean Whether mail was sent successfully
     */
    public function requestEmailChange()
    {
        return Yii::$app->mailer->compose('user/email-change-request', [
                                    'content' => $this->changeEmail,
                                    'userEmail' => $this->email,
                                ])
                                ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->params['companyName']])
                                ->setTo(Yii::$app->params['adminEmail'])
                                ->setSubject(Yii::t('mail', 'USER_REQUEST_EMAIL_CHANGE_SUBJECT'))
                                ->send();
    }

    /**
     * Changes user password
     *
     * @return boolean|integer The number of rows affected, or false if validation fails
     */
    public function changePassword()
    {
        $this->setPassword($this->newPassword);
        $this->setPasswordExpiration();
        return $this->update();
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
     * Creates new user account when user signs up through company invitation
     *
     * @param string $email New user account email
     * @return bool Whether user account were created successfully
     */
    public function createByInvitation($email)
    {
        $this->setAttribute('email', $email);
        $this->setPassword($this->password);
        $this->generateAuthKey();
        $this->setPasswordExpiration();
        $this->setToken();
        $this->suggestions_token = Yii::$app->security->generateRandomString();
        $this->scenario = self::SCENARIO_SIGN_UP_INVITATION_SERVER;
        $transaction = Yii::$app->db->beginTransaction();

        if (!$this->save() || !UserLanguage::create($this->id, $this->language) || !$this->sendSignUpConfirmation()) {
            $transaction->rollBack();
            return false;
        }

        $transaction->commit();
        return true;
    }

    /**
     * Returns provided user number of current credits
     *
     * @param null|integer $userId User ID
     * @return false|null|string
     */
    public static function getCurrentCredits($userId = null)
    {
        if (is_null($userId)) {
            $userId = Yii::$app->user->id;
        }

        return self::find()->select('current_credits')->where(['id' => $userId])->scalar();
    }

    /**
     * Returns provided user number of advertisement credits
     *
     * @param null|integer $userId User ID
     * @return false|null|string
     */
    public static function getServiceCredits($userId = null)
    {
        if (is_null($userId)) {
            $userId = Yii::$app->user->id;
        }

        return self::find()->select('service_credits')->where(['id' => $userId])->scalar();
    }

    /**
     * Updates user current credits
     *
     * @param null|integer $credits Number of current user credits
     * @return boolean Whether updated successfully
     */
    public function updateCurrentCredits($credits = null)
    {
        $this->current_credits = $credits;
        $this->scenario = self::SCENARIO_UPDATE_CURRENT_CREDITS;
        return $this->save();
    }

    /**
     * Updates user current credits
     *
     * @param null|integer $credits Number of current user credits
     * @return boolean Whether updated successfully
     */
    public function updateServiceCredits($credits = null)
    {
        if (is_null($this->service_credits)) {
            $this->service_credits = $credits;
        } else {
            $this->service_credits += $credits;
        }
        $this->scenario = self::SCENARIO_UPDATE_SERVICE_CREDITS;
        return $this->save();
    }

    /**
     * Sends email to user informing that payment was successful
     *
     * @return boolean Whether mail was sent successfully
     */
    public function sendSuccessfulPayment()
    {
        $isSent = Yii::$app->mailer->compose('user/successful-payment', [
            'companyName' => Yii::$app->params['companyName'],
        ])
            ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->params['companyName']])
            ->setTo($this->email)
            ->setSubject(Yii::t('mail', 'USER_SUCCESSFUL_PAYMENT_SUBJECT', [
                'companyName' => Yii::$app->params['companyName'],
            ]))
            ->send();

        if ($isSent) {
            $systemMessagePlaceholder = SystemMessage::PLACEHOLDER_USER_RECEIVED_SUCCESSFUL_PAYMENT_EMAIL;
            Log::user(SystemMessage::ACTION, $systemMessagePlaceholder, [], $this->id);
        }

        return $isSent;
    }
    
    /**
     * Changes class for company owner when changing company info
     *
     * @param integer $class new class
     * @param integer $newOwnerId new owner id
     * @return boolean
     */
    public function changeClass($class, $newOwnerId)
    {
        $newUser = self::find()->where([self::tableName() . '.id' => $newOwnerId])->one();
        $newUser->scenario = self::SCENARIO_CHANGE_COMPANY_CLASS;
        $newUser->class = $class;
        $this->class = $class;
        return $newUser->update();
    }

    /**
     * Returns user name and surname
     *
     * @return string
     */
    public function getNameAndSurname()
    {
        return $this->name . ' ' . $this->surname;
    }

    /**
     * Returns user company model or null if company not found
     *
     * @return null|Company
     */
    public function getCompany()
    {
        $company = Company::findByOwner($this->id);
        if (!is_null($company)) {
            return $company;
        }

        $company = Company::findByUser($this->id);
        if (!is_null($company)) {
            return $company;
        }

        return null;
    }

    /**
     * Forms and returns string with all user languages
     *
     * @return string
     */
    public function getLanguagesString()
    {
        $string = '';
        foreach ($this->userLanguages as $userLanguage) {
            $flagIcon = Icon::show(strtolower($userLanguage->language->country_code), [], Icon::FI);
            $string .= $flagIcon . ' ' . $userLanguage->language->name . ', ';
        }

        return rtrim($string, ', ');
    }

    /**
     * Returns translated user archive values
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

    /**
     * Returns list of user languages IDs
     *
     * @return array
     */
    public function getLanguagesIds()
    {
        $ids = [];
        foreach ($this->userLanguages as $userLanguage) {
            array_push($ids, $userLanguage->language->id);
        }

        return $ids;
    }

    /**
     * Converts user blocked until time to timestamp
     */
    public function convertBlockedUntilToTimestamp()
    {
        $this->blocked_until = strtotime($this->blocked_until);
    }

    /**
     * Checks whether user is blocked
     *
     * @return boolean
     */
    public function isBlocked()
    {
        if (is_null($this->blocked_until)) {
            return false;
        }

        return $this->blocked_until > time();
    }

    /**
     * Sends email to user informing that new account was created with corresponding email
     *
     * @return boolean Whether email was sent successfully
     */
    public function informAboutNewAccount()
    {
        $isSent = Yii::$app->mailer->compose('user/inform-about-new-account', [
            'companyName' => Yii::$app->params['companyName'],
        ])
            ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->params['companyName']])
            ->setTo($this->email)
            ->setSubject(Yii::t('mail', 'USER_INFORM_ABOUT_NEW_ACCOUNT_SUBJECT', [
                'companyName' => Yii::$app->params['companyName'],
            ]))
            ->send();

        if ($isSent) {
            Log::user(SystemMessage::ACTION, SystemMessage::PLACEHOLDER_USER_RECEIVED_SIGN_UP_EMAIL, [], $this->id);
        }

        return $isSent;
    }

    /**
     * Converts user last login time to string date and time
     *
     * @return false|string
     */
    public function convertLastLoginToString()
    {
        return empty($this->last_login) ? Yii::t('yii', '(not set)') : date('Y-m-d H:i:s', $this->last_login);
    }

    /**
     * Finds and returns all users that belongs to provided company
     *
     * @param null|integer $id Company ID
     * @return array|ActiveRecord[]
     */
    public static function findAllCompanyUsers($id)
    {
        $owner = self::find()
            ->joinWith('companies')
            ->where([Company::tableName() . '.id' => $id]);
        $companyUsers = self::find()
            ->joinWith('companyUser')
            ->joinWith('companyUser.company')
            ->where([Company::tableName() . '.id' => $id]);
        $users = self::find()
            ->from(['user' => $owner->union($companyUsers, true)])
            ->all();

        return $users;
    }

    /**
     * Sets current user credits
     *
     * @param null|integer $credits Number of credits that need to be set
     */
    public function setCurrentCredits($credits)
    {
        $this->current_credits = $credits;
    }

    /**
     * Sets current user credits
     *
     * @param null|integer $credits Number of credits that need to be set
     */
    public function setServiceCredits($credits)
    {
        $this->service_credits = $credits;
    }

    /**
     * Spends presented number of user credits
     *
     * @param integer $spend How many credits user spends
     */
    public function useServiceCredits($spend = 1)
    {
        $this->service_credits -= $spend;
    }

    /**
     * Spends specified user service credit amount
     * If user service credits not enough user subscription time is used as credits
     *
     * @param integer $credits How many credits user spends
     */
    public function useCombinedServiceCredits($credits)
    {
        if ($this->service_credits > $credits) {
            $spendServiceCredits = $credits;
            $spendSubscriptionCredits = 0;
        } else {
            $spendServiceCredits = $this->service_credits;
            $spendSubscriptionCredits = $credits - $spendServiceCredits;
        }
        
        if ($spendServiceCredits > 0) {
            $currentScenario = $this->scenario;
            $this->service_credits -= $spendServiceCredits;
            $this->setScenario(self::SCENARIO_UPDATE_SERVICE_CREDITS);
            $this->save();
            $this->setScenario($currentScenario);
        }
        
        if ($spendSubscriptionCredits > 0) {
            $this->useSubscriptionCredits($spendSubscriptionCredits);
        }
    }

    /**
     * Spends presented number of user credits
     *
     * @param integer $spend How many credits user spends
     */
    public function useCredits($spend = 1)
    {
        $this->current_credits -= $spend;
    }

    /**
     * Checks whether current user has credits
     *
     * @return boolean
     */
    public function hasCredits()
    {
        return $this->current_credits > self::CURRENT_CREDITS_MIN_VALUE;
    }

    /**
     * Checks whether current user has enough credits to pay for load extension
     *
     * @return boolean
     */
    public function canPayForLoadExtension()
    {
        return $this->current_credits >= Load::EXPIRED_LOAD_REACTIVATION_CREDITS;
    }

    /**
     * Checks whether current user has enough credits to pay for new load announcement
     *
     * @return boolean
     */
    public function canPayForLoadAnnouncement()
    {
        return $this->current_credits >= Load::LOAD_ANNOUNCEMENT_CREDITS;
    }

    /**
     * Checks whether current user has at least one active subscription
     *
     * @return boolean
     */
    public function hasSubscription()
    {
        $has = false;
        /** @var UserServiceActive $activeService */
        foreach ($this->userServiceActives as $activeService) {
            $service = $activeService->service;
            if ($activeService->isActive() && 
                ($service->service_type_id === ServiceType::MEMBER_TYPE_ID ||
                 $service->service_type_id === ServiceType::TRIAL_TYPE_ID)
            ) {
                $has = true;
            }
        }

        return $has;
    }
    
    /**
     * Return current user active subscription with max expiry time
     * 
     * @return boolean
     */
    public function getSubscription()
    {
        return $this->getUserServiceActives()
            ->innerJoin('service', 'service.id = user_service_active.service_id')
            ->where([
                'service.service_type_id' => ServiceType::MEMBER_TYPE_ID,
                'status' => UserServiceActive::ACTIVE,
            ])
            ->andWhere(['>', 'end_date', time()])
            ->orderBy('end_date DESC')->one();
    }
    
    /**
     * Returns subscription credits from subscription time
     */
    public function getSubscriptionCredits()
    {
        $activeService = $this->getSubscription();
        return is_null($activeService) ? 0 : $activeService->getAvailableCreditsFromEndTime();
    }
    
    /**
     * Returns subscription end time
     *
     * @param string formatted date
     */
    public function getSubscriptionEndTime()
    {
        $activeService = $this->getSubscription();
        return is_null($activeService) ? '' : date('Y-m-d H:i', $activeService->end_date);
    }
    
    /**
     * Returns user service credits plus subscription credits
     *
     * @return integer
     */
    public function getCombinedServiceCredits()
    {
        $serviceCredits = $this->service_credits;
        $subscriptionCredits = $this->getSubscriptionCredits();
        return $serviceCredits + $subscriptionCredits;
    }
    
    /**
     * Spends specified amount of credits from subscription expiry time
     *
     * @param integer $credits
     */
    public function useSubscriptionCredits($credits)
    {
        $activeService = $this->getSubscription();
        $endDate = $activeService->useTimeAsCredits($credits);
        
        $service = $activeService->getRelatedUserService();
        $service->end_date = $endDate;
        $service->setScenario(UserService::SCENARIO_SYSTEM_SETS_END_DATE);
        $service->save();
        $activeService->removeIfExpired();
    }

    /**
     * Sends email to user informing about expired subscription
     *
     * @return boolean Whether email was sent successfully
     */
    public function informAboutExpiredSubscription()
    {
        $userLanguageIds = UserLanguage::getUserLanguages($this->id);
        
        MailLanguage::setMailLanguage($userLanguageIds);
        
        $isSent = Yii::$app->mailer->compose('user/inform-about-expired-subscription', [
            'companyName' => Yii::$app->params['companyName'],
        ])
            ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->params['companyName']])
            ->setTo($this->email)
            ->setSubject(Yii::t('mail', 'INFORM_USER_ABOUT_EXPIRED_SUBSCRIPTION_SUBJECT', [
                'companyName' => Yii::$app->params['companyName'],
            ]))
            ->send();

        if ($isSent) {
            Log::user(SystemMessage::ACTION, SystemMessage::PLACEHOLDER_USER_RECEIVED_EXPIRED_SUBSCRIPTION_EMAIL, [], $this->id);
        }

        return $isSent;
    }

    /**
     * Sets default user email
     */
    public function setDefaultEmail()
    {
        $body = 'email';
        $ending = '@auto-loads.lt';
        $i = 1;
        $invalid = true;
        while ($invalid) {
            $this->email = $body . $i . $ending;
            if ($this->validate(['email'])) {
                $invalid = false;
            }
            $i++;
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
     * Checks whether current user account type is natural
     *
     * @return boolean
     */
    public function isNatural()
    {
        return $this->account_type == self::NATURAL;
    }

    /**
     * Checks whether current user account type is legal
     *
     * @return boolean
     */
    public function isLegal()
    {
        return $this->account_type == self::LEGAL;
    }

    /**
     * Checks whether user class is invalid
     *
     * @return boolean
     */
    public function hasInvalidClass()
    {
        return is_null($this->class) || is_null($this->original_class);
    }

    /**
     * Checks whether user is archived
     *
     * @return boolean
     */
    public function isArchived()
    {
        return $this->archive == self::ARCHIVED;
    }
    
    /**
     * Checks whether user is active
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->active == self::ACTIVE;
    }

    /**
     * Checks whether blocked until is expired
     *
     * @return boolean
     */
    public function expiredBlockedUntil()
    {
        return !is_null($this->blocked_until) && $this->blocked_until < time();
    }

    /**
     * Archives user or multiple users
     *
     * @param integer|array $id List of users IDs or specific user ID to be archived
     * @return integer Number of archived users
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
     * Inactives user or multiple users
     *
     * @param integer|array $id List of users IDs or specific user ID to be archived
     * @return integer Number of archived users
     */
    public static function inactives($id)
    {
        return self::updateAll([
            'active' => self::INACTIVE,
            'archive' => self::ARCHIVED,
        ], compact('id'));
    }
    
    /**
     * Unarchives user or multiple users
     *
     * @param integer|array $id List of users IDs or specific user ID to be archived
     * @return integer Number of unarchived users
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
     * Checks whether user class is carrier
     *
     * @return boolean
     */
    public function isCarrier()
    {
        return $this->class == self::CARRIER;
    }

    /**
     * Changes user class to supplier
     *
     * @return false|integer The number of rows affected, or false if validation fails
     */
    public function makeSupplier()
    {
        $this->class = self::SUPPLIER;
        $this->scenario = self::SCENARIO_SYSTEM_MAKES_USER_AS_SUPPLIER;
        return $this->update(true, ['class']);
    }

    /**
     * Removes blocked status from specific user
     *
     * @param integer $id User ID that blocked status needs to be removed
     * @return integer
     */
    public static function removeBlockedUntil($id)
    {
        return self::updateAll([
            'allow' => self::ALLOWED,
            'blocked_until' => self::DEFAULT_BLOCKED_UNTIL,
        ], compact('id'));
    }

    /**
     * Checks whether user has enough credits to perform action, that requires credits
     *
     * @param integer $credits Number of credits to perform some actionCar
     * @return boolean
     */
    public function hasEnoughCredits($credits)
    {
        return $this->current_credits >= $credits;
    }

    /**
     * Checks whether user has enough service credits
     *
     * @param $credits
     * @return bool
     */
    public function hasEnoughServiceCredits($credits)
    {
        return $this->service_credits >= $credits;
    }
    
    /**
     * Checks whether user has enough service credits 
     * combined with available subscription credits
     *
     * @param $credits
     * @return bool
     */
    public function hasEnoughCombinedServiceCredits($credits)
    {
        return $this->getCombinedServiceCredits() >= $credits;
    }

    /**
     * Checks whether user has enough credits to perform action, that requires credits
     *
     * @param integer $credits Number of credits to perform some action
     * @return boolean
     */
    public function canAnnounceCarTransporter($credits)
    {
        if ($this->current_credits == self::DEFAULT_CURRENT_CREDITS) {
            return empty(CarTransporter::getCarTransportersInDateRange($this->id)); // User did not announce any load in whole week
        }
        return $this->current_credits >= $credits;
    }
    
    /**
     * finds users array for newest suggestions
     *
     * @return array of user id
     */
    public static function findUsers()
    {
        $ownerId = self::find()
                ->joinWith('companies')
                ->joinWith('userServiceActives')
                ->where([
                        self::tableName() . '.suggestions' => self::SEND_SUGGESTIONS,
                        self::tableName() . '.active' => self::ACTIVE,
                        self::tableName() . '.allow' => self::ALLOWED])
                ->andWhere(['not', [Company::tableName() . '.suggestions' => Company::DO_NOT_SEND_SUGGESTIONS]])
                ->andWhere(['not', [UserServiceActive::tableName() . '.user_id' => null]])
                ->groupBy(self::tableName() . '.id')
                ->all();
        $userId = self::find()
                ->joinWith('companyUser.company')
                ->joinWith('userServiceActives')
                ->where([
                        self::tableName() . '.suggestions' => self::SEND_SUGGESTIONS,
                        self::tableName() . '.active' => self::ACTIVE,
                        self::tableName() . '.allow' => self::ALLOWED])
                ->andWhere(['not', [Company::tableName() . '.suggestions' => Company::DO_NOT_SEND_SUGGESTIONS]])
                ->andWhere(['not', [UserServiceActive::tableName() . '.user_id' => null]])
                ->groupBy(self::tableName() . '.id')
                ->all();
        return array_merge($ownerId, $userId);
    }
	
	/**
     * Gets translated reasons from where user knew
     *
     * @return array
     */
    public function getTranslatedChoices()
    {
        return ArrayHelper::map(CameFrom::find()
                ->where(['language_id' => $this->cameFrom->language_id, 'type' => CameFrom::REASON_TO_REGISTER])
                ->all(), 'id', 'source_name');
    }

    /**
     * @param $creditType
     * @param $model
     * @param null $user
     * @return bool
     */
    public function hasBoughtService($model, $creditType = CreditService::CREDIT_TYPE_CAR_TRANSPORTER_DETAILS_VIEW, $user = null)
    {
        if (is_null($user)) {
            $user = Yii::$app->user;
        }
        if ($user->isGuest === true) {
            return false;
        }

        $service = UserCreditService::find()->where(['credit_service_type' =>  $creditType,
            'entity_id' => $model->id, 'user_id' => $user->id])->one();
        if (is_null($service)) {
            return false;
        }
        return true;
    }

}
