<?php

namespace common\models;

use Yii;
use common\models\CarTransporter;
use common\models\CreditCode;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;
use yii\web\NotFoundHttpException;
/**
 * This is the model class for table "{{%creditcode}}".
 *
 * @property integer $id
 * @property string $creditcode_id
 * @property string $cartransporter_id
 *
 * @property CreditCode $creditCode
 * @property CarTransporter $cartransporter
 */
class CreditCodeHistory extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%creditcode_history}}';
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
            [['creditcode_id', 'cartransporter_id'], 'required'],
            [['creditcode_id', 'cartransporter_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [];
    }

    /**
     * @return ActiveQuery
     */
    public function getCreditCode()
    {
        return $this->hasOne(CreditCode::className(), ['id' => 'creditcode_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCarTransporter()
    {
        return $this->hasOne(CarTransporter::className(), ['id' => 'cartransporter_id']);
    }

}
