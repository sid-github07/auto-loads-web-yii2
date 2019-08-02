<?php

use yii\db\Migration;

/**
 * Class m180822_095415_update_service_type_table_rename_adv_credits_to_service_credits
 */
class m180822_095415_update_service_type_table_rename_adv_credits_to_service_credits extends Migration
{
    /**
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $services = \common\models\ServiceType::find()->where(['id' => \common\models\ServiceType::SERVICE_CREDITS_TYPE_ID, 'name' => 'ADV_CREDITS'])->all();
        foreach ($services as $service) {
            $service->name = \common\models\Service::TITLE_SERVICE_CREDITS;
            $service->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $services = \common\models\ServiceType::find()->where(['id' => \common\models\ServiceType::SERVICE_CREDITS_TYPE_ID, 'name' => \common\models\Service::TITLE_SERVICE_CREDITS])->all();
        foreach ($services as $service) {
            $service->name = 'ADV_CREDITS';
            $service->save();
        }
    }
}
