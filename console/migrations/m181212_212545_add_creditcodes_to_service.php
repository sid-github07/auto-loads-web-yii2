<?php

use yii\db\Migration;
use common\models\Service;
use common\models\ServiceType;


/**
 * Class m181212_212545_service
 * Insert the Services for the Creditcode feature that unregistered user can
 * buy a creditcode and do some action with it (like show conact info)
 */
class m181212_212545_add_creditcodes_to_service extends Migration
{
    public function safeUp()
    {
        $this->insert(Service::tableName(), [
            'service_type_id' => ServiceType::CREDITCODE_TYPE_ID,
            'days' => 0,
            'price' => '5.0',
            'name' => Service::TITLE_CREDITSCODE_20,
            'credits' => 20,
            'label' => 'CODE FOR 20 CREDITS',
            'desc' => 'Code for unregistered users with amount of 20 Credits',
            'setup_fee' => '0.0',
            'administration_fee' => '0.0',
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert(Service::tableName(), [
            'service_type_id' => ServiceType::CREDITCODE_TYPE_ID,
            'days' => 0,
            'price' => '20.0',
            'name' => Service::TITLE_CREDITSCODE_100,
            'credits' => 100,
            'label' => 'CODE FOR 100 CREDITS',
            'desc' => 'Code for unregistered users with amount of 100 Credits',
            'setup_fee' => '0.0',
            'administration_fee' => '0.0',
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert(Service::tableName(), [
            'service_type_id' => ServiceType::CREDITCODE_TYPE_ID,
            'days' => 0,
            'price' => '150.0',
            'name' => Service::TITLE_CREDITSCODE_1000,
            'credits' => 1000,
            'label' => 'CODE FOR 1000 CREDITS',
            'desc' => 'Code for unregistered users with amount of 1000 Credits',
            'setup_fee' => '0.0',
            'administration_fee' => '0.0',
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    public function safeDown()
    {
        $this->delete(ServiceType::tableName(), ['service_type_id' => ServiceType::CREDITCODE_TYPE_ID]);
    }
}
