<?php

namespace common\models;

use Yii;
use common\models\CarTransporter;
use common\models\CreditService;
use common\models\UserService;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;
use yii\web\NotFoundHttpException;
/**
 * This is the model class for table "{{%creditcode}}".
 *
 * @property integer $id
 * @property string $user_service_id
 * @property string $creditcode
 * @property string $credits
 * @property string $creditsleft
 *
 * @property UserService $userService
 */
class CreditCode extends ActiveRecord
{

    /** @const integer The length a creditcode has. Db needs to be changed, too. */
    const CREDIT_CODE_LENGTH = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%creditcode}}';
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
            [['creditcode', 'credits', 'creditsleft'], 'required'],
            [['creditcode'], 'string', 'length' => 10],
            [['user_service_id', 'creditsleft', 'credits'], 'integer'],
            [['creditcode'], 'unique'],
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
    public function getUserService()
    {
        return $this->hasOne(UserService::className(), ['id' => 'user_service_id']);
    }

    /**
     * Generates a unique, random credit code. When a code already exists create
     * new one (should actually not happen).
     * @return string
     */
    public function generateCreditCode() 
    {
        $code = Yii::$app->getSecurity()->generateRandomString(10);
        if(CreditCode::find()->where(['creditcode' => $code])->exists()) {
            return Yii::$app->getSecurity()->generateRandomString(10);
        }
        return $code;
    }

    /**
     * Checks if credit is enough to cover certain costs
     * @return string
     */
    public function isPaid() 
    {
        return $this->userService->paid == UserService::PAID;
    }

    /**
     * Checks if credit is enough to cover certain costs
     * @return bool
     */
    public function canBuy($costs) 
    {
        return ($this->isPaid() && $this->creditsleft >= $costs);
    }

    /**
     * Checks if credit is enough to cover certain costs
     * @return bool
     */
    public function buy(CarTransporter $carTransporter, CreditService $creditService) 
    {
        if (!$this->isPaid()) {
            return false;
        }
        $this->creditsleft -= $creditService->credit_cost;
        if ($this->save()) {
            $history = new CreditCodeHistory();
            $history->creditcode_id = $this->id;
            $history->cartransporter_id = $carTransporter->id;
            $history->save();
            return true;
        }
        return false;
    }

    /**
     * Checks if creditcode has already paid for this transporter preview
     * @return bool
     */
    public function hasBought(CarTransporter $carTransporter, CreditService $creditService) 
    {
        return CreditCodeHistory::find()
            ->where(['cartransporter_id' => $carTransporter->id, 'creditcode_id'=> $this->id])
            ->exists();
    }

}
