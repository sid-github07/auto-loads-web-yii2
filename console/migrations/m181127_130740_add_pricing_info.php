<?php

use yii\db\Migration;
use common\models\Service;

/**
 * Class m181127_130740_add_pricing_info
 */
class m181127_130740_add_pricing_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!$this->existColumn('service', 'setup_fee')) {
            $this->addColumn('service', 'setup_fee', 
                $this->decimal(10, 2)
                ->null()
                ->after('desc')
                ->comment('Pajungimo mokestis')
            );
        }
        
        if (!$this->existColumn('service', 'administration_fee')) {
            $this->addColumn('service', 'administration_fee', 
                $this->decimal(10, 2)
                ->null()
                ->after('setup_fee')
                ->comment('Administravimo mokestis')
            );
        }
        
        Yii::$app->db->createCommand()
            ->update('service', [
                'setup_fee' => 0.80,
                'administration_fee' => 1.50,
            ], ['name' => Service::TITLE_MEMBER_024]
            )->execute();
        
        Yii::$app->db->createCommand()
            ->update('service', [
                'setup_fee' => 0,
                'administration_fee' => 1.50,
            ], ['name' => Service::TITLE_MEMBER_1]
            )->execute();
        
        Yii::$app->db->createCommand()
            ->update('service', [
                'setup_fee' => 0.80,
                'administration_fee' => 1.50,
            ], ['name' => Service::TITLE_BASIC_CREDITS_20]
            )->execute();
        
        Yii::$app->db->createCommand()
            ->update('service', [
                'setup_fee' => 0,
                'administration_fee' => 1.50,
            ], ['name' => Service::TITLE_PREMIUM_CREDITS_45]
            )->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if ($this->existColumn('service', 'setup_fee')) {
            $this->dropColumn('service', 'setup_fee');
        }
        
        if ($this->existColumn('service', 'administration_fee')) {
            $this->dropColumn('service', 'administration_fee');
        }
    }
    
    /**
     * Checks whether column exists
     * 
     * @param string $table
     * @param string $column
     * @return boolean
     */
    private function existColumn($table, $column)
    {
        $schema = Yii::$app->db->schema->getTableSchema($table);
        return isset($schema->columns[$column]);
    }
}
