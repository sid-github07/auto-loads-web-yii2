<?php

use common\models\ServiceType;
use yii\db\Migration;

/**
 * Class m161006_074029_service_type_data
 */
class m161006_074029_service_type_data extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->insert(ServiceType::tableName(), [
            'id' => ServiceType::MEMBER_TYPE_ID,
            'name' => 'MEMBER',
            'order_by_user' => ServiceType::ALLOWED_FOR_USER,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert(ServiceType::tableName(), [
            'id' => ServiceType::CREDITS_TYPE_ID,
            'name' => 'CREDITS',
            'order_by_user' => ServiceType::FORBIDDEN_FOR_USER,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert(ServiceType::tableName(), [
            'id' => ServiceType::TRIAL_TYPE_ID,
            'name' => 'TRIAL',
            'order_by_user' => ServiceType::FORBIDDEN_FOR_USER,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->truncateTable(ServiceType::tableName());
    }
}
