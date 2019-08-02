<?php

use yii\db\Migration;

/**
 * Class m180821_090919_update_user_table_rename_adv_credits_to_service_credits
 */
class m180821_090919_update_user_table_rename_adv_credits_to_service_credits extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn(\common\models\User::tableName(), 'adv_credits', 'service_credits');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn(\common\models\User::tableName(), 'service_credits', 'adv_credits');
    }
}
