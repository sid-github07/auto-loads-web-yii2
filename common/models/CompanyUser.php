<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%company_user}}".
 *
 * @property integer $company_id
 * @property integer $user_id
 *
 * @property User $user
 * @property Company $company
 */
class CompanyUser extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%company_user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Company ID
            ['company_id', 'required', 'message' => Yii::t('app', 'COMPANY_USER_COMPANY_ID_IS_REQUIRED')],
            ['company_id', 'integer', 'message' => Yii::t('app', 'COMPANY_USER_COMPANY_ID_IS_INTEGER')],
            ['company_id', 'exist', 'targetClass' => Company::className(),
                                'targetAttribute' => ['company_id' => 'id'],
                                        'message' => Yii::t('app', 'COMPANY_USER_COMPANY_ID_NOT_EXIST')],

            // User ID
            ['user_id', 'required', 'message' => Yii::t('app', 'COMPANY_USER_USER_ID_IS_REQUIRED')],
            ['user_id', 'integer', 'message' => Yii::t('app', 'COMPANY_USER_USER_ID_IS_INTEGER')],
            ['user_id', 'unique', 'targetClass' => self::className(),
                                      'message' => Yii::t('app', 'COMPANY_USER_USER_ID_IS_NOT_UNIQUE')],
            ['user_id', 'exist', 'targetClass' => User::className(),
                             'targetAttribute' => ['user_id' => 'id'],
                                     'message' => Yii::t('app', 'COMPANY_USER_USER_ID_NOT_EXIST')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'company_id' => Yii::t('app', 'COMPANY_USER_COMPANY_ID_LABEL'),
            'user_id' => Yii::t('app', 'COMPANY_USER_USER_ID_LABEL'),
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
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }

    /**
     * Assigns user to company
     *
     * @param null|integer $companyId Company ID
     * @param null|integer $userId User ID
     * @return boolean Whether user was assigned to company successfully
     */
    public static function assign($companyId = null, $userId = null)
    {
        $model = new self();
        $model->setAttribute('company_id', $companyId);
        $model->setAttribute('user_id', $userId);
        return $model->save();
    }

    /**
     * Returns company ID by given user
     *
     * @param null|integer $userId User ID
     * @return false|null|string
     */
    public static function getCompanyId($userId = null)
    {
        if (is_null($userId)) {
            $userId = Yii::$app->getUser()->getId();
        }

        return self::find()->select('company_id')->where(['user_id' => $userId])->scalar();
    }
    
    /**
     * Changes company owner to simple user
     * 
     * @param type $oldOwnerId
     * @return type
     */
    public function changeOwnerToSimpleUser($oldOwnerId = null) 
    {
        $this->user_id = $oldOwnerId;
        return $this->update();
    }
}
