<?php

use yii\db\Migration;
use common\models\Load;

/**
 * Class m190512_084853_update_load_table_add_transported_column
 */
class m190512_084853_update_load_table_add_transported_column extends Migration
{
    private $tableName;
    
    public function init() {
        $this->tableName = Load::tableName();
    }
    
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $schema = Yii::$app->db->schema->getTableSchema($this->tableName);
        
        if (!isset($schema->columns['transported'])) {
            $this->addColumn($this->tableName, 'transported', 
                "smallint(6) NOT NULL DEFAULT " . Load::DEFAULT_TRANSPORTED .
                " COMMENT 'Žymi transportuotą krovinį' AFTER `active`");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'transported');
    }
}
