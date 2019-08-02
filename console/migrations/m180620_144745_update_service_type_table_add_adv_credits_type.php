<?php

use yii\db\Migration;
use common\models\ServiceType;
use yii\db\Query;
use common\models\Service;


/**
 * Class m180620_144745_update_service_type_table_add_adv_credits_type
 */
class m180620_144745_update_service_type_table_add_adv_credits_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert(ServiceType::tableName(), [
            'id' => ServiceType::SERVICE_CREDITS_TYPE_ID,
            'name' => ServiceType::SERVICE_TYPE_SERVICE_CREDITS,
            'order_by_user' => ServiceType::ALLOWED_FOR_USER,
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

        (new Query)
            ->createCommand()
            ->delete(ServiceType::tableName(), ['id' => ServiceType::SERVICE_CREDITS_TYPE_ID])
            ->execute();

    }
}
