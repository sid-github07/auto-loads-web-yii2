<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "prepared_values".
 *
 * @property integer $id
 * @property integer $total_cars_ready
 * @property integer $total_cars_transported
 * @property integer $created_at
 * @property integer $updated_at
 */
class PreparedValue extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'prepared_values';
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
            // total_cars_ready
            ['total_cars_ready', 'required'],
            ['total_cars_ready', 'integer'],
            
            // total_cars_ready
            ['total_cars_transported', 'required'],
            ['total_cars_transported', 'integer'],

            // created_at
            ['created_at', 'integer'],

            // updated_at
            ['updated_at', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'PREPARED_VALUE_LABEL_ID'),
            'total_cars_ready' => Yii::t('app', 'PREPARED_VALUE_LABEL_ID'),
            'total_cars_transported' => Yii::t('app', 'PREPARED_VALUE_LABEL_ID'),
            'created_at' => Yii::t('app', 'PREPARED_VALUE_LABEL_ID'),
            'updated_at' => Yii::t('app', 'PREPARED_VALUE_LABEL_ID'),
        ];
    }
    
    /**
     * Loads model
     * 
     * @return PrepareValue model 
     * @throws NotFoundHttpException
     */
    public static function loadModel()
    {
        $model = self::find()->one();
        if (is_null($model)) {
            throw new NotFoundHttpException(Yii::t('alert', 'MODEL_NOT_FOUND', 
                ['class' => basename(str_replace('\\', '/', static::class))])
            );
        }
        return $model;
    }
}
