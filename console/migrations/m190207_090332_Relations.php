<?php

use yii\db\Migration;

class m190207_090332_Relations extends Migration
{

    public function init()
    {
       $this->db = 'db';
       parent::init();
    }

    public function safeUp()
    {
        $this->addForeignKey('fk_load_opened_contacts_opened_by',
            '{{%load_opened_contacts}}','opened_by',
            '{{%user}}','id',
            'NO ACTION','NO ACTION'
         );
        $this->addForeignKey('fk_load_opened_contacts_service_id',
            '{{%load_opened_contacts}}','service_id',
            '{{%credit_services}}','id',
            'NO ACTION','NO ACTION'
         );
        $this->addForeignKey('fk_load_opened_contacts_user_id',
            '{{%load_opened_contacts}}','user_id',
            '{{%user}}','id',
            'NO ACTION','NO ACTION'
         );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_load_opened_contacts_opened_by', '{{%load_opened_contacts}}');
        $this->dropForeignKey('fk_load_opened_contacts_service_id', '{{%load_opened_contacts}}');
        $this->dropForeignKey('fk_load_opened_contacts_user_id', '{{%load_opened_contacts}}');
    }
}
