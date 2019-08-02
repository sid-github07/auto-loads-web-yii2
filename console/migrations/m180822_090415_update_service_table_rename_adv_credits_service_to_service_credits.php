<?php

use yii\db\Migration;
use common\models\ServiceType;
use common\models\Service;

/**
 * Class m180822_090415_update_service_table_rename_adv_credits_service_to_service_credits
 */
class m180822_090415_update_service_table_rename_adv_credits_service_to_service_credits extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $services = Service::find()->where(['service_type_id' => ServiceType::SERVICE_CREDITS_TYPE_ID, 'name' => 'AD_CREDITS'])->all();
        foreach ($services as $service) {
            $service->name = Service::TITLE_SERVICE_CREDITS;
            $service->desc = null;
            $service->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $services = Service::find()->where(['service_type_id' => ServiceType::SERVICE_CREDITS_TYPE_ID, 'name' => Service::TITLE_SERVICE_CREDITS])->all();
        foreach ($services as $service) {
            $service->name = 'AD_CREDITS';
            $service->save();
        }
    }
}
