<?php

use yii\db\Migration;
use common\models\CreditService;

/**
 * Handles the creation of table `credit_services`.
 */
class m180820_120000_create_credit_services_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('credit_services', [
            'id' => $this->primaryKey(),
            'credit_type' => $this->tinyInteger(),
            'credit_cost' => $this->integer(),
            'status' => $this->tinyInteger()->defaultValue(\common\models\CreditService::STATUS_ACTIVE),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()->null()
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        $this->insert(CreditService::tableName(), [
            'credit_type' => CreditService::CREDIT_TYPE_CAR_TRANSPORTER_DETAILS_VIEW,
            'credit_cost' => 4,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('credit_services');
    }
}
