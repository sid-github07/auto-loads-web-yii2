<?php

use common\models\Service;
use common\models\ServiceType;
use yii\db\Migration;

/**
 * Class m161006_074451_service_data
 */
class m161006_074451_service_data extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->insert(Service::tableName(), [
            'id' => 1,
            'service_type_id' => ServiceType::MEMBER_TYPE_ID,
            'days' => 30,
            'price' => 17.00,
            'name' => Service::TITLE_MEMBER_1,
            'credits' => 500,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert(Service::tableName(), [
            'id' => 2,
            'service_type_id' => ServiceType::MEMBER_TYPE_ID,
            'days' => 365,
            'price' => 149.00,
            'name' => Service::TITLE_MEMBER_12,
            'credits' => 500,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert(Service::tableName(), [
            'id' => 3,
            'service_type_id' => ServiceType::CREDITS_TYPE_ID,
            'days' => 30,
            'price' => 0.00,
            'name' => Service::TITLE_CREDITS_200,
            'credits' => 200,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert(Service::tableName(), [
            'id' => 4,
            'service_type_id' => ServiceType::TRIAL_TYPE_ID,
            'days' => 1,
            'price' => 0.00,
            'name' => Service::TITLE_TRIAL,
            'credits' => 500,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert(Service::tableName(), [
            'id' => 5,
            'service_type_id' => ServiceType::MEMBER_TYPE_ID,
            'days' => 1,
            'price' => 10.00,
            'name' => Service::TITLE_MEMBER_024,
            'credits' => 500,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->truncateTable(Service::tableName());
    }
}
