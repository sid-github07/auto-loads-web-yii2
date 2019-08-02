<?php

use yii\db\Migration;

/**
 * Class m180621_082804_update_service_table_add_label_description_columns
 */
class m180620_145657_update_service_table_add_label_description_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(\common\models\Service::tableName(), 'label', \yii\db\Schema::TYPE_STRING . ' DEFAULT NULL AFTER credits ');
        $this->addColumn(\common\models\Service::tableName(), 'desc', \yii\db\Schema::TYPE_TEXT . ' DEFAULT NULL AFTER label ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(\common\models\Service::tableName(), 'label');
        $this->dropColumn(\common\models\Service::tableName(), 'desc');
    }

}
