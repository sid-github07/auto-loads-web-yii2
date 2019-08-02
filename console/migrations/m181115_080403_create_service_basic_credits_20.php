<?php

use yii\db\Migration;
use common\models\Service;
use common\models\ServiceType;
use common\models\UserInvoice;
use common\models\UserService;
use common\models\UserServiceActive;
use yii\helpers\ArrayHelper;

/**
 * Class m181115_080403_create_service_basic_credits_20
 */
class m181115_080403_create_service_basic_credits_20 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $records = Service::find()->where([
            'name' => Service::TITLE_BASIC_CREDITS_20
        ])->all();
        
        if (count($records) == 0) {
            $this->insert(Service::tableName(), [
                'service_type_id' => ServiceType::SERVICE_CREDITS_TYPE_ID,
                'days' => 0,
                'price' => 5.00,
                'name' => Service::TITLE_BASIC_CREDITS_20,
                'label' => 'Basic Credits 20',
                'desc' => 'Subscription credit price is linked to this service',
                'credits' => 20,
                'created_at' => time(),
                'updated_at' => time(),
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $service = Service::find()->where([
            'name' => Service::TITLE_BASIC_CREDITS_20,
        ])->one();

        $userServices = UserService::find()->where(['service_id' => $service->id])
            ->select(['id'])->all();
        $userServiceIds = ArrayHelper::map($userServices, 'id', 'id');
        
        UserInvoice::deleteAll(['user_service_id' => $userServiceIds]);
        UserService::deleteAll(['id' => $userServiceIds]);
        
        UserServiceActive::deleteAll(['service_id' => $service->id]);
        
        $service->delete();
    }
}
