<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "load_opened_contacts".
 *
 * @property int $record_id
 * @property int $service_id
 * @property int $user_id
 * @property int $load_id
 * @property int $opened_by
 * @property int $paid
 * @property string $used
 * @property string $date
 *
 * @property User $openedBy
 * @property CreditService $service
 * @property User $user
 */
class LoadOpenedContacts extends \yii\db\ActiveRecord
{
    const USED_CREDITS = 'credits';
    const USED_SUBSCRIPTION = 'subscription';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'load_opened_contacts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service_id', 'user_id', 'load_id', 'opened_by', 'paid', 'used'], 'required'],
            [['service_id', 'user_id', 'load_id', 'opened_by', 'paid'], 'integer'],
            [['used'], 'string'],
            [['date'], 'safe'],
            [['service_id', 'user_id', 'opened_by'], 'unique', 'targetAttribute' => ['service_id', 'load_id', 'user_id', 'opened_by']],
            [['opened_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['opened_by' => 'id']],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => CreditService::className(), 'targetAttribute' => ['service_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            ['used', 'in', 'range' => [self::USED_CREDITS, self::USED_SUBSCRIPTION]]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'record_id' => 'Record ID',
            'service_id' => 'Service ID',
            'user_id' => 'User ID',
            'load_id' => 'Load ID',
            'opened_by' => 'Opened By',
            'paid' => 'Paid',
            'used' => 'Used',
            'date' => 'Date',
        ];
    }

    /**
     * @param int $user_id
     * @param Load $load
     * @param CreditService $service
     * @param bool $freeForSubscribers
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function logOpen($user_id, $load, $service, $freeForSubscribers = true){
        /** @var User $openedBy */
        $openedBy = Yii::$app->getUser()->getIdentity();
        if (Yii::$app->getUser()->getIsGuest()) {
            throw new \Exception("Unregistered user");
        }
        $userIdEntity = User::findOne($user_id);
        if (!$userIdEntity instanceof User) {
            throw new \Exception('User not found');
        }
        if (!$service instanceof CreditService) {
            throw new \Exception("Credit service not found");
        }
        $this->opened_by = $openedBy->id;
        $this->service_id = $service->id;
        $this->user_id = $userIdEntity->id;
        $this->paid = $service->credit_cost;
        $this->load_id = $load->id;
        // Check if already opened
        if (self::find()->where($this->getAttributes(['service_id', 'user_id', 'opened_by', 'load_id']))->count()) {
            return true;
        }
        $transaction = Yii::$app->getDb()->beginTransaction();
        if ($freeForSubscribers && $openedBy->hasSubscription()) {
            $this->paid = 0;
            $this->used = self::USED_SUBSCRIPTION;
        } elseif ($openedBy->service_credits >= $service->credit_cost) {
            $openedBy->useServiceCredits($service->credit_cost);
            $openedBy->updateServiceCredits();
            $this->used = self::USED_CREDITS;
        } elseif ($openedBy->hasSubscription() && $openedBy->getSubscriptionCredits() >= $service->credit_cost) {
            $openedBy->useSubscriptionCredits($service->credit_cost);
            $this->used = self::USED_SUBSCRIPTION;
        } else {
            $this->addError('not_enought_credits', Yii::t('alert', 'NOT_ENOUGH_SERVICE_CREDITS'));
        }
        if (!$this->hasErrors()) {
            if ($this->save()) {
                $transaction->commit();
                return true;
            }
        }
        $transaction->rollBack();
        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOpenedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'opened_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(CreditService::className(), ['id' => 'service_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getLoad(){
        return $this->hasOne(Load::className(), ['load_id' => 'id']);
    }
}
