<?php

use yii\db\Migration;
use common\models\CreditCode;
use common\models\UserService;

/**
 * Handles the creation of table `creditcode`.
 */
class m181214_165940_create_creditcode_table extends Migration
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

        $this->createTable(CreditCode::tableName(), [
            'id' => $this->primaryKey(),
            'user_service_id' => $this->integer()
                ->null()
                ->defaultValue(null),
            'creditcode' => $this->char(10)
                ->notNull()
                ->unique()
                ->comment('Unique Creditcode with length of 10 characters'),
            'credits' => $this->integer()
                ->notNull()
                ->comment('Amount of credits'),
            'creditsleft' => $this->integer()
                ->notNull()
                ->comment('The amount of credits on this code left.'),
            'created_at' => $this->integer()
                ->notNull(),
            'updated_at' => $this->integer()
                ->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'user_service_fk_1',
            CreditCode::tableName(),
            'user_service_id',
            UserService::tableName(),
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('user_service_fk_1', CreditCode::tableName());
        $this->dropTable(CreditCode::tableName());
    }
}
