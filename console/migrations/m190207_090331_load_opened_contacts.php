<?php

use yii\db\Migration;

class m190207_090331_load_opened_contacts extends Migration
{

    public function init()
    {
        $this->db = 'db';
        parent::init();
    }

    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB';

        $this->createTable(
            '{{%load_opened_contacts}}',
            [
                'record_id'=> $this->primaryKey(11),
                'service_id'=> $this->integer(11)->notNull(),
                'user_id'=> $this->integer(11)->notNull(),
                'load_id'=> $this->integer(11)->notNull(),
                'opened_by'=> $this->integer(11)->notNull(),
                'paid'=> $this->integer(11)->notNull(),
                'used'=> "enum('credits', 'subscription') NOT NULL",
                'date'=> $this->timestamp()->null()->defaultExpression("CURRENT_TIMESTAMP"),
            ],$tableOptions
        );
        $this->createIndex('unique_open','{{%load_opened_contacts}}',['service_id','user_id','opened_by','load_id'],true);
        $this->createIndex('opened_by','{{%load_opened_contacts}}',['opened_by'],false);
        $this->createIndex('user_id','{{%load_opened_contacts}}',['user_id'],false);
        $this->createIndex('by_service_and_opened_by','{{%load_opened_contacts}}',['service_id','opened_by','load_id'],false);
        $this->createIndex('by_service_and_user','{{%load_opened_contacts}}',['service_id','user_id','load_id'],false);

    }

    public function safeDown()
    {
        $this->dropIndex('unique_open', '{{%load_opened_contacts}}');
        $this->dropIndex('opened_by', '{{%load_opened_contacts}}');
        $this->dropIndex('user_id', '{{%load_opened_contacts}}');
        $this->dropIndex('by_service_and_opened_by', '{{%load_opened_contacts}}');
        $this->dropIndex('by_service_and_user', '{{%load_opened_contacts}}');
        $this->dropTable('{{%load_opened_contacts}}');
    }
}
