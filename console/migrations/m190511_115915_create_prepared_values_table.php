<?php

use yii\db\Migration;
use yii\db\Expression;

/**
 * Handles the creation of table `prepared_values`.
 *
 * ./yii migrate/create create_prepared_values_table --interactive=0
 * ./yii migrate --interactive=0
 * ./yii migrate/down 1 --interactive=0
 * ./yii migrate/redo 1 --interactive=0
 *
 */
class m190511_115915_create_prepared_values_table extends Migration
{
    private $tableName = 'prepared_values';
    
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->notNull(),
            'total_cars_ready' => $this->integer()->notNull(),
            'total_cars_transported' => $this->integer()->notNull(),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
        ], $tableOptions);
        
        $now = new Expression('UNIX_TIMESTAMP(NOW())');
    
        $this->insert($this->tableName, [
            'total_cars_ready' => 0,
            'total_cars_transported' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable($this->tableName);
    }
}
