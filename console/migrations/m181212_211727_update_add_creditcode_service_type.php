<?php

use yii\db\Migration;
use common\models\ServiceType;

/**
 * Class m181212_211727_service_type
 * Adds CreditCode ServiceType which is needed for the creditcode feature that
 * unregistered user can buy those creditcode.
 */
class m181212_211727_update_add_creditcode_service_type extends Migration
{

    public function safeUp()
    {
        $this->insert(ServiceType::tableName(), [
            'id' => ServiceType::CREDITCODE_TYPE_ID,
            'name' => 'CREDITCODE',
            'order_by_user' => ServiceType::FORBIDDEN_FOR_USER,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    public function safeDown()
    {
        $this->delete(ServiceType::tableName(), ['id' => ServiceType::CREDITCODE_TYPE_ID]);
    }

}
