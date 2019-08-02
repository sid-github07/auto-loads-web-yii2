<?php

use yii\db\Schema;
use yii\db\Migration;

class m190218_101256_credit_servicesDataInsert extends Migration
{

    public function init()
    {
        $this->db = 'db';
        parent::init();
    }

    public function safeUp()
    {
        $this->batchInsert('{{%credit_services}}',
                           ["id", "credit_type", "credit_cost", "status", "created_at", "updated_at"],
                            [
    [
        'id' => '8',
        'credit_type' => '8',
        'credit_cost' => '1',
        'status' => '1',
        'created_at' => '2019-02-18 00:00:00',
        'updated_at' => '2019-02-18 00:00:00',
    ],
]
        );
    }

    public function safeDown()
    {
        $this->delete('{{%credit_services}}', ['id' => [8]]);
    }
}
