<?php

use yii\db\Migration;

class m190206_161422_credit_servicesDataInsert extends Migration
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
                    'id' => '5',
                    'credit_type' => '5',
                    'credit_cost' => '1',
                    'status' => '1',
                    'created_at' => '2019-02-06 00:00:00',
                    'updated_at' => '2019-02-06 00:00:00',
                ],
                [
                    'id' => '6',
                    'credit_type' => '6',
                    'credit_cost' => '1',
                    'status' => '1',
                    'created_at' => '2019-02-06 00:00:00',
                    'updated_at' => '2019-02-06 00:00:00',
                ],
                [
                    'id' => '7',
                    'credit_type' => '7',
                    'credit_cost' => '5',
                    'status' => '1',
                    'created_at' => '2019-02-06 00:00:00',
                    'updated_at' => '2019-02-06 00:00:00',
                ],
            ]
        );
    }

    public function safeDown()
    {
        $this->delete('{{%credit_services}}', ['id' => [5, 6, 7]]);
    }
}
