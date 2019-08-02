<?php

use yii\db\Migration;
use common\models\LoggingActivatedEmailServices;

/**
 * Class m190505_147800_create_logging_activated_email_services
 */
class m190505_147800_create_logging_activated_email_services extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(LoggingActivatedEmailServices::tableName(), [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()
                ->notNull()
                ->comment('The user id'),
            'load_id' => $this->integer()
                ->notNull()
                ->comment('For which load service is going to enable or disable.'),
            'log_activated' => $this->boolean()
                ->defaultValue(0)
                ->comment('Check if service is enabled or disabled.'),
            'created_at' => $this->integer()
                ->notNull(),
            'updated_at' => $this->integer()
                ->notNull(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(LoggingActivatedEmailServices::tableName());
    }
}
