<?php

use yii\db\Migration;

/**
 * Class m180625_134552_update_car_transport_table_add_car_pos_adv_days_columns
 */
class m180625_134552_update_car_transport_table_add_car_pos_adv_days_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(\common\models\CarTransporter::tableName(), 'car_pos_adv', \yii\db\Schema::TYPE_INTEGER . ' DEFAULT 0 AFTER quantity ');
        $this->addColumn(\common\models\CarTransporter::tableName(), 'days_adv', \yii\db\Schema::TYPE_INTEGER . ' DEFAULT 0 AFTER quantity ');
        $this->addColumn(\common\models\CarTransporter::tableName(), 'submit_time_adv', \yii\db\Schema::TYPE_DATETIME . ' DEFAULT NULL AFTER quantity ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(\common\models\CarTransporter::tableName(), 'car_pos_adv');
        $this->dropColumn(\common\models\CarTransporter::tableName(), 'days_adv');
        $this->dropColumn(\common\models\CarTransporter::tableName(), 'submit_time_adv');
    }
}
