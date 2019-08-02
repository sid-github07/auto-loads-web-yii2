<?php

use yii\db\Migration;
use common\models\User;

/**
 * Class m181218_001004_change_user_remove_unique
 */
class m181218_001004_change_user_remove_unique extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex('email', User::tableName());
        $this->dropIndex('company_code', User::tableName());
        $this->dropIndex('company_name', User::tableName());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->createIndex('email', User::tableName(), 'email', true);
        $this->createIndex('company_code', User::tableName(), 'company_code', true);
        $this->createIndex('company_name', User::tableName(), 'company_name', true);
        return true;
    }
}
