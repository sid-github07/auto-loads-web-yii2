<?php

use yii\db\Migration;
use common\models\ServiceType;
use common\models\Service;
use yii\db\Query;

/**
 * Class m180620_145657_update_service_table_add_adv_credits_services_data
 */
class m180621_082804_update_service_table_add_adv_credits_services_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert(Service::tableName(), [
            'service_type_id' => ServiceType::SERVICE_CREDITS_TYPE_ID,
            'days' => 0,
            'price' => 10.00,
            'name' => Service::TITLE_SERVICE_CREDITS,
            'label' => 'CREDITS 20',
            'desc' => 'Service credits 20 for the price of 10.00',
            'credits' => 20,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $services = Service::find()->where('service_type_id = ' . ServiceType::SERVICE_CREDITS_TYPE_ID)->select(['id'])->all();
        $serviceIds = \yii\helpers\ArrayHelper::map($services, 'id', 'id');

        (new Query)
            ->createCommand()
            ->delete(\common\models\UserService::tableName(), ['service_id' => $serviceIds])
            ->execute();

        (new Query)
            ->createCommand()
            ->delete(\common\models\UserServiceActive::tableName(), ['service_id' => $serviceIds])
            ->execute();

        (new Query)
            ->createCommand()
            ->delete(Service::tableName(), ['service_type_id' => ServiceType::SERVICE_CREDITS_TYPE_ID])
            ->execute();
    }

}
