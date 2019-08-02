<?php

namespace common\models;

use borales\extensions\phoneInput\PhoneInputValidator;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%admin}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $surname
 * @property string $email
 * @property string $phone
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property integer $admin
 * @property integer $archived
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property AdminAsUser[] $adminAsUsers
 * @property CompanyComment[] $companyComments
 */
class Admin extends ActiveRecord implements IdentityInterface
{
    /** @const integer Admin type is Admin*/
    const IS_ADMIN = 1;
    
    /** @const integer Admin type is not Admin*/
    const IS_MODERATOR = 0;
    
    /** @const integer Admin is active*/
    const NOT_ARCHIVED = 0;
    
    /** @const integer Admin is  not active active*/
    const ARCHIVED = 1;
    
    /** @const integer Maximum length of Admin authentication key */
    const AUTH_KEY_MAX_LENGTH = 32;
    
    /** @const integer Admin is active*/
    const DEFAULT_ACCOUNT_STATUS = 0;
    
    /** @const null Default password reset token value */
    const DEFAULT_PASSWORD_RESET_TOKEN = null;
    
    /** @const integer Admin name minimum length */
    const NAME_MIN_LENGTH = 2;
    
    /** @const integer Admin name maximum length */
    const NAME_MAX_LENGTH = 255;
    
    /** @const integer Admin surname minimum length */
    const SURNAME_MIN_LENGTH = 2;
    
    /** @const integer Admin surname maximum length */
    const SURNAME_MAX_LENGTH = 255;
    
    /** @const integer Maximum length of Admin email */
    const EMAIL_MAX_LENGTH = 255;
    
    /** @const integer Minimum length of Admin password */
    const PASSWORD_MIN_LENGTH = 6;
    
    /** @const integer Maximum length of Admin password */
    const PASSWORD_MAX_LENGTH = 255;
    
    /** @const string Model scenario when admin tries to log in */
    const SCENARIO_ADMIN_LOG_IN = 'admin-log-in';
    
    /** @const string Model scenario when Admin logs in */
    const SCENARIO_LOGIN_SERVER = 'update-last-login';

    /** @const string Model scenario when administrator creates new administrator/moderator */
    const SCENARIO_ADMIN_ADDS_NEW_ADMIN = 'admin-adds-new-admin';

    /** @const string Model scenario when system saves new administrator/moderator */
    const SCENARIO_SYSTEM_SAVES_NEW_ADMIN = 'system-saves-new-admin';

    /** @const string Model scenario when administrator edits other administrator/moderator information */
    const SCENARIO_ADMIN_EDITS_INFO = 'admin-edits-info';

    /** @const string Model scenario when administrator changes other administrator/moderator password */
    const SCENARIO_ADMIN_CHANGES_PASSWORD = 'admin-changes-password';

    /** @const string Model scenario when system saves administrator/moderator password */
    const SCENARIO_SYSTEM_SAVES_PASSWORD = 'system-saves-password';

    /** @const string Model scenario when administrator/moderator changes self profile */
    const SCENARIO_ADMIN_CHANGES_SELF_PROFILE = 'admin-changes-self-profile';

    /** @const string Model scenario when administrator/moderator changes self password */
    const SCENARIO_ADMIN_CHANGES_SELF_PASSWORD = 'admin-changes-self-password';

    /** @const string Model scenario when system migrates administrator/moderator data from one database to another */
    const SCENARIO_SYSTEM_MIGRATES_ADMIN_DATA = 'system-migrates-admin-data';

    const SCENARIO_SYSTEM_MIGRATES_ADMIN = 'system-migrates-admin';

    /** @var string Current administrator/moderator password */
    public $oldPassword;

    /** @var string Current administrator/moderator new password */
    public $newPassword;

    /** @var string Current administrator/moderator repeated new password */
    public $repeatNewPassword;

    /** @var string  admin password for password edit */
    public $passwordEdit; // FIXME
    
    /** @var string Admin password */
    public $password;
    
    /** @var string Repeated admin password */
    public $repeatPassword;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin}}';
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
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_ADMIN_LOG_IN] = [
            'email',
            'password',
        ];
        $scenarios[self::SCENARIO_LOGIN_SERVER] = [
            'updated_at',
        ];
        $scenarios[self::SCENARIO_ADMIN_ADDS_NEW_ADMIN] = [
            'name',
            'surname',
            'email',
            'phone',
            'password',
            'repeatPassword',
            'admin',
        ];
        $scenarios[self::SCENARIO_SYSTEM_SAVES_NEW_ADMIN] = [
            'name',
            'surname',
            'email',
            'phone',
            'auth_key',
            'password_hash',
            'password_reset_token',
            'admin',
            'archived',
        ];
        $scenarios[self::SCENARIO_ADMIN_EDITS_INFO] = [
            'name',
            'surname',
            'phone',
            'admin',
        ];
        $scenarios[self::SCENARIO_ADMIN_CHANGES_PASSWORD] = [
            'password',
            'repeatPassword',
        ];
        $scenarios[self::SCENARIO_SYSTEM_SAVES_PASSWORD] = [
            'password_hash',
        ];
        $scenarios[self::SCENARIO_ADMIN_CHANGES_SELF_PROFILE] = [
            'name',
            'surname',
            'phone',
        ];
        $scenarios[self::SCENARIO_ADMIN_CHANGES_SELF_PASSWORD] = [
            'oldPassword',
            'newPassword',
            'repeatNewPassword',
        ];
        $scenarios[self::SCENARIO_SYSTEM_MIGRATES_ADMIN_DATA] = [
            'id',
            'name',
            'surname',
            'email',
            'phone',
            'auth_key',
            'password_hash',
            'password_reset_token',
            'admin',
            'archived',
            'created_at',
            'updated_at',
        ];
        $scenarios[self::SCENARIO_SYSTEM_MIGRATES_ADMIN] = [
            'id',
            'name',
            'surname',
            'email',
            'phone',
            'auth_key',
            'password_hash',
            'password_reset_token',
            'admin',
            'archived',
            'created_at',
            'updated_at',
        ];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Name
            ['name', 'required', 'message' => Yii::t('app', 'ADMIN_NAME_IS_REQUIRED')],
            ['name', 'filter', 'filter' => 'trim'],
            ['name', 'string', 'min' => self::NAME_MIN_LENGTH,
                          'tooShort' => Yii::t('app', 'ADMIN_NAME_IS_TOO_SHORT', [
                              'length' => self::NAME_MIN_LENGTH,
                          ]),
                               'max' => self::NAME_MAX_LENGTH,
                           'tooLong' => Yii::t('app', 'ADMIN_NAME_IS_TOO_LONG', [
                               'length' => self::NAME_MAX_LENGTH,
                           ])],
            ['name', 'validateName', 'params' => [
                'message' => Yii::t('app', 'ADMIN_NAME_IS_NOT_MATCH'),
            ]],
            
            // Surname
            ['surname', 'required', 'message' => Yii::t('app', 'ADMIN_SURNAME_IS_REQUIRED')],
            ['surname', 'filter', 'filter' => 'trim'],
            ['surname', 'string', 'min' => self::SURNAME_MIN_LENGTH,
                             'tooShort' => Yii::t('app', 'ADMIN_SURNAME_IS_TOO_SHORT', [
                                 'length' => self::SURNAME_MIN_LENGTH,
                             ]),
                                  'max' => self::SURNAME_MAX_LENGTH,
                              'tooLong' => Yii::t('app', 'ADMIN_SURNAME_IS_TOO_LONG', [
                                  'length' => self::SURNAME_MAX_LENGTH,
                              ])],
            ['surname', 'validateName', 'params' => [
                'message' => Yii::t('app', 'ADMIN_SURNAME_IS_NOT_MATCH'),
            ]],
            
            // Email
            ['email', 'required', 'message' => Yii::t('app', 'ADMIN_EMAIL_IS_REQUIRED')],
            ['email', 'email', 'message' => Yii::t('app', 'ADMIN_EMAIL_IS_NOT_EMAIL')],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'unique', 'targetClass' => '\common\models\Admin',
                                    'message' => Yii::t('app', 'ADMIN_EMAIL_IS_NOT_UNIQUE', [
                                        'adminEmail' => Yii::$app->params['adminEmail']
                                    ]),
                                     'except' => [
                                         self::SCENARIO_ADMIN_LOG_IN,
                                     ],
                ],
            ['email', 'string', 'max' => self::EMAIL_MAX_LENGTH,
                            'tooLong' => Yii::t('app', 'ADMIN_EMAIL_IS_TOO_LONG', [
                                'length' => self::EMAIL_MAX_LENGTH,
                            ])],
            
            // Phone
            ['phone', PhoneInputValidator::className(), 'message' => Yii::t('app', 'ADMIN_PHONE_IS_NOT_MATCH'),
                'except' => self::SCENARIO_SYSTEM_MIGRATES_ADMIN],
            
            // Auth_key
            ['auth_key', 'required', 'message' => Yii::t('app', 'ADMIN_AUTH_KEY_IS_REQUIRED')],
            ['auth_key', 'string', 'max' => self::AUTH_KEY_MAX_LENGTH,
                               'tooLong' => Yii::t('app', 'ADMIN_AUTH_KEY_IS_TOO_LONG', [
                                   'length' => self::AUTH_KEY_MAX_LENGTH,
                               ])],

            // Password hash
            ['password_hash', 'required', 'message' => Yii::t('app', 'ADMIN_PASSWORD_HASH_IS_REQUIRED')],
            ['password_hash', 'string', 'message' => Yii::t('app', 'ADMIN_PASSWORD_HASH_IS_NOT_STRING')],
            
            // Password reset token
            ['password_reset_token', 'string', 'message' => Yii::t('app', 'ADMIN_PASSWORD_RESET_TOKEN_IS_NOT_STRING')],
            ['password_reset_token', 'unique', 'targetClass' => '\common\models\Admin',
                                                   'message' => Yii::t('app', 'ADMIN_PASSWORD_RESET_TOKEN_IS_NOT_UNIQUE')],
            ['password_reset_token', 'default', 'value' => self::DEFAULT_PASSWORD_RESET_TOKEN],
            
            // PasswordEdit // FIXME
            ['passwordEdit', 'required', 'message' => Yii::t('app', 'ADMIN_PASSWORD_IS_REQUIRED')],
            ['passwordEdit', 'string', 'min' => self::PASSWORD_MIN_LENGTH,
                              'tooShort' => Yii::t('app', 'ADMIN_PASSWORD_IS_TOO_SHORT', [
                                  'length' => self::PASSWORD_MIN_LENGTH,
                              ]),
                                   'max' => self::PASSWORD_MAX_LENGTH,
                               'tooLong' => Yii::t('app', 'ADMIN_PASSWORD_IS_TOO_LONG', [
                                   'length' => self::PASSWORD_MAX_LENGTH,
                               ])],
            
            // Password
            ['password', 'required', 'message' => Yii::t('app', 'ADMIN_PASSWORD_IS_REQUIRED'),
                'except' => self::SCENARIO_SYSTEM_MIGRATES_ADMIN],
            ['password', 'string', 'min' => self::PASSWORD_MIN_LENGTH,
                              'tooShort' => Yii::t('app', 'ADMIN_PASSWORD_IS_TOO_SHORT', [
                                  'length' => self::PASSWORD_MIN_LENGTH,
                              ]),
                                   'max' => self::PASSWORD_MAX_LENGTH,
                               'tooLong' => Yii::t('app', 'ADMIN_PASSWORD_IS_TOO_LONG', [
                                   'length' => self::PASSWORD_MAX_LENGTH,
                               ])],
            
            // Repeat password
            ['repeatPassword', 'required', 'message' => Yii::t('app', 'ADMIN_REPEAT_PASSWORD_IS_REQUIRED')],
            ['repeatPassword', 'compare', 'compareAttribute' => 'password',
                                                    'message' => Yii::t('app', 'ADMIN_REPEAT_PASSWORD_IS_NOT_MATCH')],
            
            // Admin
            ['admin', 'required', 'message' => Yii::t('app', 'ADMIN_ROLE_IS_REQUIIRED')],
            ['admin', 'in', 'range' => [self::IS_MODERATOR, self::IS_ADMIN],
                           'message' => Yii::t('app', 'LOAD_ACTIVE_IS_NOT_IN_RANGE')],
            ['admin', 'integer', 'message' => Yii::t('app', 'LOAD_STATUS_IS_NOT_INTEGER')],
            
            // Archived
            ['archived', 'default', 'value' => self::DEFAULT_ACCOUNT_STATUS],
            ['archived', 'integer', 'message' => Yii::t('app', 'ADMIN_STATUS_IS_NOT_INTEGER')],
            ['archived', 'in', 'range' => [self::NOT_ARCHIVED, self::ARCHIVED],
                           'message' => Yii::t('app', 'ADMIN_STATUS_IS_NOT_IN_RANGE')],
            
            // Created at
            ['created_at', 'integer', 'message' => Yii::t('app', 'ADMIN_CREATED_AT_IS_NOT_INTEGER')],

            // Updated at
            ['updated_at', 'integer', 'message' => Yii::t('app', 'ADMIN_UPDATED_AT_IS_NOT_INTEGER')],

            // Old password
            ['oldPassword', 'required', 'message' => Yii::t('app', 'ADMIN_PASSWORD_IS_REQUIRED')],
            ['oldPassword', 'string', 'min' => self::PASSWORD_MIN_LENGTH,
                                 'tooShort' => Yii::t('app', 'ADMIN_PASSWORD_IS_TOO_SHORT', [
                                     'length' => self::PASSWORD_MIN_LENGTH,
                                 ]),
                                      'max' => self::PASSWORD_MAX_LENGTH,
                                  'tooLong' => Yii::t('app', 'ADMIN_PASSWORD_IS_TOO_LONG', [
                                      'length' => self::PASSWORD_MAX_LENGTH,
                                  ])],

            // New password
            ['newPassword', 'required', 'message' => Yii::t('app', 'ADMIN_PASSWORD_IS_REQUIRED')],
            ['newPassword', 'string', 'min' => self::PASSWORD_MIN_LENGTH,
                                 'tooShort' => Yii::t('app', 'ADMIN_PASSWORD_IS_TOO_SHORT', [
                                     'length' => self::PASSWORD_MIN_LENGTH,
                                 ]),
                                      'max' => self::PASSWORD_MAX_LENGTH,
                                  'tooLong' => Yii::t('app', 'ADMIN_PASSWORD_IS_TOO_LONG', [
                                      'length' => self::PASSWORD_MAX_LENGTH,
                                  ])],

            // Repeat new password
            ['repeatNewPassword', 'required', 'message' => Yii::t('app', 'ADMIN_PASSWORD_IS_REQUIRED')],
            ['repeatNewPassword', 'compare', 'compareAttribute' => 'newPassword',
                                                   'message' => Yii::t('app', 'ADMIN_REPEAT_PASSWORD_IS_NOT_MATCH')],
            ['repeatNewPassword', 'string', 'min' => self::PASSWORD_MIN_LENGTH,
                                       'tooShort' => Yii::t('app', 'ADMIN_PASSWORD_IS_TOO_SHORT', [
                                           'length' => self::PASSWORD_MIN_LENGTH,
                                       ]),
                                            'max' => self::PASSWORD_MAX_LENGTH,
                                        'tooLong' => Yii::t('app', 'ADMIN_PASSWORD_IS_TOO_LONG', [
                                            'length' => self::PASSWORD_MAX_LENGTH,
                                        ])],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'surname' => 'Surname',
            'email' => 'Email',
            'phone' => 'Phone',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'admin' => 'Admin',
            'archived' => 'Archived',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAdminAsUsers()
    {
        return $this->hasMany(AdminAsUser::className(), ['admin_id' => 'id']);
    }
    
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
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
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @return ActiveQuery
     */
    public function getCompanyComments()
    {
        return $this->hasMany(CompanyComment::className(), ['admin_id' => 'id']);
    }
    
    /**
     * Checks if administrator with password and login exist in database.
     * 
     * @return boolean
     */
    public function login() 
    {
        if (!$this->validate()) {
            return false;
        }
        
        $admin = self::findByEmail($this->email);
        if (is_null($admin)) {
            return false;
        }
        
        if ($admin->isNotArchived() && $admin->validatePassword($this->password)) {
            return Yii::$app->admin->login($admin);
        }
        
        return false;
    }
    
    public function isNotArchived() 
    {
        return $this->archived == Admin::NOT_ARCHIVED;
    }
    
    /**
     * Finds admin by email
     *
     * @param string $email Admin email that model must be found
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return self::findOne(['email' => $email]);
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
     * Checks whether admin is admin
     *
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->admin == self::IS_ADMIN;
    }
    
    /**
     * Checks whether admin is moderator
     *
     * @return boolean
     */
    public function isModerator()
    {
        return $this->admin == self::IS_MODERATOR;
    }
    
    /**
     * Validates admin name or surname
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
     * Returns all translated available administrator roles
     *
     * @return array
     */
    public static function getTranslatedRoles()
    {
        return [
            self::IS_ADMIN => Yii::t('element', 'CHOOSE_ADMINISTRATOR_ROLE'),
            self::IS_MODERATOR => Yii::t('element', 'CHOOSE_MODERATOR_ROLE'),
        ];
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
     * Finds all admins and transfers them to active data provider
     * 
     * @return ActiveDataProvider list of admins
     */
    public static function getDataProvider()
    {
        $query = self::find()
            ->where(['<>', self::tableName() . '.id', Yii::$app->admin->identity->id])
            ->andWhere([self::tableName() . '.archived' => Admin::NOT_ARCHIVED]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        
        return $dataProvider;
    }

    /**
     * Returns administrator name and surname
     *
     * @return string
     */
    public function getNameAndSurname()
    {
        return $this->name . ' ' . $this->surname;
    }

    /**
     * Returns administrator role name depending on administrator role
     *
     * @return string
     */
    public function getRoleName()
    {
        switch ($this->admin) {
            case self::IS_MODERATOR:
                return Yii::t('text', 'TEXT_FOR_MODERATOR_ROLE');
            case self::IS_ADMIN:
                return Yii::t('text', 'TEXT_FOR_ADMINISTRATOR_ROLE');
            default:
                return Yii::t('yii', '(not set)');
        }
    }

    /**
     * Formats administrator/moderator phone number to match validation
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
}
