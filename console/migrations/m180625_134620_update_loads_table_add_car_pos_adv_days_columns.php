<?php

use yii\db\Migration;

/**
 * Class m180625_134620_update_loads_table_add_car_pos_adv_days_columns
 */
class m180625_134620_update_loads_table_add_car_pos_adv_days_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(\common\models\Load::tableName(), 'car_pos_adv', \yii\db\Schema::TYPE_INTEGER . ' DEFAULT 0 AFTER price ');
        $this->addColumn(\common\models\Load::tableName(), 'days_adv', \yii\db\Schema::TYPE_INTEGER . ' DEFAULT 0 AFTER price ');
        $this->addColumn(\common\models\Load::tableName(), 'submit_time_adv', \yii\db\Schema::TYPE_DATETIME . ' DEFAULT NULL AFTER price ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(\common\models\Load::tableName(), 'car_pos_adv');
        $this->dropColumn(\common\models\Load::tableName(), 'days_adv');
        $this->dropColumn(\common\models\Load::tableName(), 'submit_time_adv');
    }
}
