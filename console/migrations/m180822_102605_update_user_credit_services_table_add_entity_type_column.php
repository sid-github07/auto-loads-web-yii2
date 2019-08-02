<?php

use yii\db\Migration;

/**
 * Class m180822_102605_update_user_credit_services_table_add_entity_type_column
 */
class m180822_102605_update_user_credit_services_table_add_entity_type_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(\common\models\UserCreditService::tableName(), 'entity_type', 'string');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(\common\models\UserCreditService::tableName(), 'entity_type');
    }
}
