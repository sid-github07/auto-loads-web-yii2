<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%company_comment}}".
 *
 * @property integer $id
 * @property integer $company_id
 * @property integer $admin_id
 * @property string $comment
 * @property integer $archived
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Company $company
 * @property Admin $admin
 */
class CompanyComment extends ActiveRecord
{
    /** @const integer Comment is not archived */
    const NOT_ARCHIVED = 0;

    /** @const integer Comment is archived */
    const ARCHIVED = 1;

    /** @const integer Maximum number of characters that company comment can contain */
    const COMMENT_MAX_LENGTH = 2000;

    /** @const string Model scenario when administrator adds company comment */
    const SCENARIO_ADMIN_ADDS_COMPANY_COMMENT = 'ADMIN_ADDS_COMPANY_COMMENT';

    /** @const string Model scenario when system saves company comment */
    const SCENARIO_SYSTEM_SAVES_COMPANY_COMMENT = 'SYSTEM_SAVES_COMPANY_COMMENT';

    /** @const string Model scenario when system migrates company comments data from one database to another */
    const SCENARIO_SYSTEM_MIGRATES_COMPANY_COMMENTS_DATA = 'system-migrates-company-comments-data';

    const SCENARIO_SYSTEM_MIGRATES_COMPANY_COMMENTS = 'system-migrates-company-comments';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%company_comment}}';
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
        $scenarios[self::SCENARIO_ADMIN_ADDS_COMPANY_COMMENT] = ['comment'];
        $scenarios[self::SCENARIO_SYSTEM_SAVES_COMPANY_COMMENT] = [
            'company_id',
            'admin_id',
            'comment',
            'archived',
        ];
        $scenarios[self::SCENARIO_SYSTEM_MIGRATES_COMPANY_COMMENTS_DATA] = [
            'id',
            'company_id',
            'admin_id',
            'comment',
            'archived',
            'created_at',
            'updated_at',
        ];
        $scenarios[self::SCENARIO_SYSTEM_MIGRATES_COMPANY_COMMENTS] = [
            'id',
            'company_id',
            'admin_id',
            'comment',
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
            // Company ID
            ['company_id', 'required', 'message' => Yii::t('app', 'COMPANY_COMMENT_COMPANY_ID_IS_REQUIRED')],
            ['company_id', 'integer', 'message' => Yii::t('app', 'COMPANY_COMMENT_COMPANY_ID_IS_NOT_INTEGER')],
            ['company_id', 'exist', 'targetClass' => Company::className(),
                                'targetAttribute' => ['company_id' => 'id'],
                                        'message' => Yii::t('app', 'COMPANY_COMMENT_COMPANY_ID_NOT_EXIST')],

            // Admin ID
            ['admin_id', 'required', 'message' => Yii::t('app', 'COMPANY_COMMENT_ADMIN_ID_IS_REQUIRED')],
            ['admin_id', 'integer', 'message' => Yii::t('app', 'COMPANY_COMMENT_ADMIN_ID_IS_NOT_INTEGER')],
            ['admin_id', 'exist', 'targetClass' => Admin::className(),
                              'targetAttribute' => ['admin_id' => 'id'],
                                      'message' => Yii::t('app', 'COMPANY_COMMENT_ADMIN_ID_NOT_EXIST')],

            // Comment
            ['comment', 'required', 'message' => Yii::t('app', 'COMPANY_COMMENT_COMMENT_IS_REQUIRED')],
            ['comment', 'string', 'max' => self::COMMENT_MAX_LENGTH,
                              'tooLong' => Yii::t('app', 'COMPANY_COMMENT_COMMENT_IS_TOO_LONG', [
                                  'length' => self::COMMENT_MAX_LENGTH,
                              ]),
                              'message' => Yii::t('app', 'COMPANY_COMMENT_COMMENT_IS_NOT_STRING')],
            ['comment', 'filter', 'filter' => 'trim'],

            // Archived
            ['archived', 'required', 'message' => Yii::t('app', 'COMPANY_COMMENT_ARCHIVED_IS_REQUIRED')],
            ['archived', 'integer', 'message' => Yii::t('app', 'COMPANY_COMMENT_ARCHIVED_IS_NOT_INTEGER')],
            ['archived', 'in', 'range' => array_keys(self::getArchiveStatuses()), 'message' => Yii::t('app', 'COMPANY_COMMENT_ARCHIVED_IS_NOT_IN_RANGE')],

            // Created at
            ['created_at', 'integer', 'message' => Yii::t('app', 'COMPANY_COMMENT_CREATED_AT_IS_NOT_INTEGER')],

            // Updated at
            ['updated_at', 'integer', 'message' => Yii::t('app', 'COMPANY_COMMENT_UPDATED_AT_IS_NOT_INTEGER')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'COMPANY_COMMENT_ID_LABEL'),
            'company_id' => Yii::t('app', 'COMPANY_COMMENT_COMPANY_ID_LABEL'),
            'admin_id' => Yii::t('app', 'COMPANY_COMMENT_ADMIN_ID_LABEL'),
            'comment' => Yii::t('app', 'COMPANY_COMMENT_COMMENT_LABEL'),
            'archived' => Yii::t('app', 'COMPANY_COMMENT_ARCHIVED_LABEL'),
            'created_at' => Yii::t('app', 'COMPANY_COMMENT_CREATED_AT_LABEL'),
            'updated_at' => Yii::t('app', 'COMPANY_COMMENT_UPDATED_AT_LABEL'),
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
     * @return ActiveQuery
     */
    public function getAdmin()
    {
        return $this->hasOne(Admin::className(), ['id' => 'admin_id']);
    }

    /**
     * Returns translated archives statuses
     *
     * @return array
     */
    public static function getArchiveStatuses()
    {
        return [
            self::NOT_ARCHIVED => Yii::t('app', 'COMMENT_IS_NOT_ARCHIVED'),
            self::ARCHIVED => Yii::t('app', 'COMMENT_IS_ARCHIVED'),
        ];
    }

    /**
     * Checks whether company comment is archived
     *
     * @return boolean
     */
    public function isArchived()
    {
        return $this->archived == self::ARCHIVED;
    }
}
