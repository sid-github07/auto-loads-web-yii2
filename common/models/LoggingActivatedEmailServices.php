<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;
use yii\web\NotFoundHttpException;
/**
 * This is the model class for table "{{%creditcode}}".
 *
 * @property integer $id
 * @property int $user_id
 * @property int $load_id
 *
 * @property int $log_activated
 * @property int $updated_at
 */
class LoggingActivatedEmailServices extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%logging_activated_email_services}}';
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
            [['user_id', 'load_id'], 'required'],
            [['user_id', 'load_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [];
    }
}
