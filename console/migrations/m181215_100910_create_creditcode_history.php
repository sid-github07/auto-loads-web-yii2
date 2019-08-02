<?php

use yii\db\Migration;
use common\models\CarTransporter;
use common\models\CreditCode;
use common\models\CreditCodeHistory;

/**
 * Class m181215_100910_create_creditcode_history
 */
class m181215_100910_create_creditcode_history extends Migration
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

        $this->createTable(CreditCodeHistory::tableName(), [
            'id' => $this->primaryKey(),
            'creditcode_id' => $this->integer()
                ->notNull()
                ->comment('The used creditcode'),
            'cartransporter_id' => $this->integer()
                ->notNull()
                ->comment('For which cartransport preview the code was used'),
            'created_at' => $this->integer()
                ->notNull(),
            'updated_at' => $this->integer()
                ->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'creditcode_fk_1',
            CreditCodeHistory::tableName(),
            'creditcode_id',
            CreditCode::tableName(),
            'id',
            'NO ACTION',
            'NO ACTION'
        );

        $this->addForeignKey(
            'cartransporter_fk1',
            CreditCodeHistory::tableName(),
            'cartransporter_id',
            CarTransporter::tableName(),
            'id',
            'NO ACTION',
            'NO ACTION'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('creditcode_fk_1', CreditCodeHistory::tableName());
        $this->dropTable(CreditCodeHistory::tableName());
    }
}
