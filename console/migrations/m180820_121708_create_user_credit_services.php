<?php

use yii\db\Migration;

/**
 * Class m180820_121708_create_user_credit_services
 */
class m180820_121708_create_user_credit_services extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user_credit_services', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'credit_service_id' => $this->tinyInteger(),
            'entity_id' => $this->integer(),
            'credit_service_type' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()->null()
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user_credit_services');
    }

}
