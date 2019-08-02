<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%admin_as_user}}".
 *
 * @property integer $id
 * @property integer $admin_id
 * @property integer $user_id
 * @property string $token
 * @property string $ip
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user
 * @property Admin $admin
 */
class AdminAsUser extends ActiveRecord
{
    const TOKEN_LENGTH = 64;
    const MAX_TOKEN_LENGTH = 255;
    const DEFAULT_TOKEN_VALUE = null;
    const MAX_IP_LENGTH = 255;

    const SCENARIO_ADMIN_LOGINS_TO_USER = 'admin-logins-to-user';
    const SCENARIO_SYSTEM_REMOVES_TOKEN = 'system-removes-token';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin_as_user}}';
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

        $scenarios[self::SCENARIO_ADMIN_LOGINS_TO_USER] = [
            'admin_id',
            'user_id',
            'token',
            'ip',
        ];
        $scenarios[self::SCENARIO_SYSTEM_REMOVES_TOKEN] = [
            'token',
        ];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Admin ID
            ['admin_id', 'required'],
            ['admin_id', 'integer'],
            ['admin_id', 'exist', 'targetClass' => Admin::className(), 'targetAttribute' => ['admin_id' => 'id']],

            // User ID
            ['user_id', 'required'],
            ['user_id', 'integer'],
            ['user_id', 'exist', 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],

            // Token
            ['token', 'string', 'max' => self::MAX_TOKEN_LENGTH],
            ['token', 'unique', 'targetClass' => self::className()],
            ['token', 'default', 'value' => self::DEFAULT_TOKEN_VALUE],

            // IP
            ['ip', 'required'],
            ['ip', 'string', 'max' => self::MAX_IP_LENGTH],

            // Created at
            ['created_at', 'required'],
            ['created_at', 'integer'],

            // Updated at
            ['updated_at', 'required'],
            ['updated_at', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'admin_id' => 'Admin ID',
            'user_id' => 'User ID',
            'token' => 'Token',
            'ip' => 'Ip',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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
    public function getAdmin()
    {
        return $this->hasOne(Admin::className(), ['id' => 'admin_id']);
    }

    /**
     * Generates random string as token for administrator to safely login to user account
     *
     * @param integer $length Token length
     * @return string
     */
    public static function generateToken($length = self::TOKEN_LENGTH)
    {
        return Yii::$app->security->generateRandomString($length);
    }

    /**
     * Generates URL address to user account
     *
     * @return string
     */
    public function generateLinkToUserAccount()
    {
        return Yii::$app->urlManagerFrontend->createAbsoluteUrl([
            'site/login-for-admin',
            'lang' => Yii::$app->language,
            'token' => $this->token,
        ]);
    }

    /**
     * Removes safe administrator login to user account token
     *
     * @return boolean
     */
    public function removeToken()
    {
        $this->scenario = self::SCENARIO_SYSTEM_REMOVES_TOKEN;
        $this->token = self::DEFAULT_TOKEN_VALUE;
        return $this->save();
    }
}
