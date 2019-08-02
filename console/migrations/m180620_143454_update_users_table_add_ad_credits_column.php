<?php

use yii\db\Migration;

/**
 * Class m180620_143454_update_users_table_add_ad_credits_column
 */
class m180620_143454_update_users_table_add_ad_credits_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(\common\models\User::tableName(), 'adv_credits', \yii\db\Schema::TYPE_INTEGER . " DEFAULT 0 AFTER created_at");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(\common\models\User::tableName(), 'adv_credits');
    }

}
