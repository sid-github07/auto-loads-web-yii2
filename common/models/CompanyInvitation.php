<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * This is the model class for table "{{%company_invitation}}".
 *
 * @property integer $id
 * @property integer $company_id
 * @property string $email
 * @property string $token
 * @property integer $accepted
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Company $company
 */
class CompanyInvitation extends ActiveRecord
{
    /** @const boolean Company invitation is not accepted */
    const NOT_ACCEPTED = false;

    /** @const boolean Company invitation is accepted */
    const ACCEPTED = true;

    /** @const integer Maximum number of characters that email can contain */
    const EMAIL_MAX_LENGTH = 255;

    /** @const integer Maximum number of characters that token can contain */
    const TOKEN_MAX_LENGTH = 64;

    /** @const null Default value for token */
    const DEFAULT_TOKEN_VALUE = null;

    /** @const string Model scenario when company owner sends invitation to user */
    const SCENARIO_CLIENT = 'client';

    /** @const string Model scenario when company invitation data must be saved to database */
    const SCENARIO_SERVER = 'server';

    /** @const string Model scenario when company invitation must be accepted */
    const SCENARIO_ACCEPT = 'accept';

    /** @const string Model scenario when system migrates company invitation data */
    const SCENARIO_SYSTEM_MIGRATES_COMPANY_INVITATION_DATA = 'system-migrates-company-invitation-data';

    const SCENARIO_SYSTEM_MIGRATES_COMPANY_INVITATION = 'system-migrates-company-invitation';

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_CLIENT => [
                'email',
            ],
            self::SCENARIO_SERVER => [
                'company_id',
                'email',
                'token',
                'accepted',
            ],
            self::SCENARIO_ACCEPT => [
                'accepted',
            ],
            self::SCENARIO_SYSTEM_MIGRATES_COMPANY_INVITATION_DATA => [
                'id',
                'company_id',
                'email',
                'token',
                'accepted',
                'created_at',
                'updated_at',
            ],
            self::SCENARIO_SYSTEM_MIGRATES_COMPANY_INVITATION => [
                'id',
                'company_id',
                'email',
                'token',
                'accepted',
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
        return '{{%company_invitation}}';
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
            // Company ID
            ['company_id', 'required', 'message' => Yii::t('app', 'COMPANY_INVITATION_COMPANY_ID_IS_REQUIRED')],
            ['company_id', 'integer', 'message' => Yii::t('app', 'COMPANY_INVITATION_COMPANY_ID_IS_NOT_INTEGER')],
            ['company_id', 'exist', 'targetClass' => Company::className(),
                                'targetAttribute' => ['company_id' => 'id'],
                                        'message' => Yii::t('app', 'COMPANY_INVITATION_COMPANY_ID_NOT_EXIST')],

            // Email
            ['email', 'required', 'message' => Yii::t('app', 'COMPANY_INVITATION_EMAIL_IS_REQUIRED')],
            ['email', 'email', 'message' => Yii::t('app', 'COMPANY_INVITATION_EMAIL_IS_NOT_EMAIL')],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'string', 'max' => self::EMAIL_MAX_LENGTH,
                            'tooLong' => Yii::t('app', 'COMPANY_INVITATION_EMAIL_IS_TOO_LONG', [
                                'length' => self::EMAIL_MAX_LENGTH,
                            ])],
            ['email', 'unique', 'targetClass' => '\common\models\User',
                                    'message' => Yii::t('app', 'COMPANY_INVITATION_EMAIL_IS_NOT_UNIQUE'),
                'except' => self::SCENARIO_SYSTEM_MIGRATES_COMPANY_INVITATION],

            // Token
            ['token', 'default', 'value' => self::DEFAULT_TOKEN_VALUE],
            ['token', 'unique', 'targetClass' => '\common\models\CompanyInvitation',
                                    'message' => Yii::t('app', 'COMPANY_INVITATION_TOKEN_IS_NOT_UNIQUE')],
            ['token', 'string', 'max' => self::TOKEN_MAX_LENGTH,
                            'tooLong' => Yii::t('app', 'COMPANY_INVITATION_TOKEN_IS_TOO_LONG', [
                                'length' => self::TOKEN_MAX_LENGTH,
                            ])],

            // Accepted
            ['accepted', 'required', 'message' => Yii::t('app', 'COMPANY_INVITATION_ACCEPTED_IS_REQUIRED')],
            ['accepted', 'in', 'range' => [self::NOT_ACCEPTED, self::ACCEPTED],
                             'message' => Yii::t('app', 'COMPANY_INVITATION_ACCEPTED_NOT_IN_RANGE')],

            // Created at
            ['created_at', 'integer', 'message' => Yii::t('app', 'COMPANY_INVITATION_CREATED_AT_IS_NOT_INTEGER')],

            // Updated at
            ['updated_at', 'integer', 'message' => Yii::t('app', 'COMPANY_INVITATION_UPDATED_AT_IS_NOT_INTEGER')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_id' => Yii::t('app', 'COMPANY_INVITATION_COMPANY_ID_LABEL'),
            'email' => Yii::t('app', 'COMPANY_INVITATION_EMAIL_LABEL'),
            'token' => Yii::t('app', 'COMPANY_INVITATION_TOKEN_LABEL'),
            'accepted' => Yii::t('app', 'COMPANY_INVITATION_ACCEPTED_LABEL'),
            'created_at' => Yii::t('app', 'COMPANY_INVITATION_CREATED_AT'),
            'updated_at' => Yii::t('app', 'COMPANY_INVITATION_UPDATED_AT'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }

    /**
     * Finds company invitation by given invitation token
     *
     * @param null|string $token Invitation token
     * @return null|static
     */
    public static function findByToken($token = null)
    {
        return self::findOne(['token' => $token, 'accepted' => self::NOT_ACCEPTED]);
    }

    /**
     * Deletes all invitations, who has provided email
     *
     * @return integer The number of rows deleted
     */
    public function deleteByEmail()
    {
        return self::deleteAll(['email' => $this->email]);
    }

    /**
     * Creates new user invitation to company entry
     *
     * @return boolean Whether entry created successfully
     */
    public function create()
    {
        $company = Company::getCompany();
        $this->setAttribute('company_id', $company->id);
        $this->setToken();
        $this->makeNotAccepted();
        return $this->save();
    }

    /**
     * Sets token
     *
     * @param integer $length Token length
     */
    private function setToken($length = self::TOKEN_MAX_LENGTH)
    {
        $this->setAttribute('token', Yii::$app->getSecurity()->generateRandomString($length));
    }

    /**
     * Makes company invitation accepted
     */
    public function makeAccepted()
    {
        $this->setAttribute('accepted', self::ACCEPTED);
    }

    /**
     * Makes company invitation not accepted
     */
    public function makeNotAccepted()
    {
        $this->setAttribute('accepted', self::NOT_ACCEPTED);
    }

    /**
     * Sends user invitation to company
     *
     * @return boolean Whether mail was sent successfully
     */
    public function send()
    {
        return Yii::$app->mailer->compose('company/invitation', [
            'url' => $this->getInvitationUrl(),
            'companyName' => Yii::$app->params['companyName'],
        ])
                                ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->params['companyName']])
                                ->setTo($this->email)
                                ->setSubject(Yii::t('mail', 'COMPANY_INVITATION_SEND_SUBJECT', [
                                    'companyName' => Yii::$app->params['companyName'],
                                ]))
                                ->send();
    }

    /**
     * Returns URL to sign up as company user
     *
     * @return string
     */
    private function getInvitationUrl()
    {
        return Url::to([
            'site/sign-up-invitation',
            'lang' => Yii::$app->language,
            'token' => $this->token,
        ], true);
    }

    /**
     * Finds company invitation by given user email
     *
     * @param null|string $email User email
     * @return null|static
     */
    public static function findByEmail($email = null)
    {
        return self::findOne(['email' => $email, 'accepted' => self::NOT_ACCEPTED]);
    }

    /**
     * Accepts company invitation
     *
     * @return boolean
     */
    public function accept()
    {
        $this->makeAccepted();
        return $this->save();
    }
}
