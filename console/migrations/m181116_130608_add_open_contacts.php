<?php

use yii\db\Migration;
use yii\db\Expression;
use common\models\CreditService;

/**
 * Class m181116_130608_add_open_contacts
 */
class m181116_130608_add_open_contacts extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!$this->existColumn('load', 'open_contacts_days')) {
            $this->addColumn('load', 'open_contacts_days', 
                $this->integer()
                ->defaultValue(0)
                ->after('car_pos_adv')
                ->comment('Atvirų kontaktų iškėlimo dienos')
            );
        }

        if (!$this->existColumn('load', 'open_contacts_expiry')) {
            $this->addColumn('load', 'open_contacts_expiry', 
                $this->integer()
                ->defaultValue(0)
                ->after('open_contacts_days')
                ->comment('Atvirų kontaktų galiojimo data')
            );
        }

        if (!$this->existColumn('car_transporter', 'open_contacts_days')) {
            $this->addColumn('car_transporter', 'open_contacts_days', 
                $this->integer()
                ->defaultValue(0)
                ->after('car_pos_adv')
                ->comment('Atvirų kontaktų iškėlimo dienos')
            );
        }

        if (!$this->existColumn('car_transporter', 'open_contacts_expiry')) {
            $this->addColumn('car_transporter', 'open_contacts_expiry', 
                $this->integer()
                ->defaultValue(0)
                ->after('open_contacts_days')
                ->comment('Atvirų kontaktų galiojimo data')
            );
        }
        
        $records = CreditService::find()->where([
            'credit_type' => CreditService::CREDIT_TYPE_OPEN_CONTACTS
        ])->all();
        
        if (count($records) == 0) {
            $this->insert('credit_services', [
                'credit_type' => CreditService::CREDIT_TYPE_OPEN_CONTACTS,
                'credit_cost' => 1,
                'status' => CreditService::STATUS_ACTIVE,
                'created_at' => new Expression('NOW()'),
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if ($this->existColumn('load', 'open_contacts_days')) {
            $this->dropColumn('load', 'open_contacts_days');
        }

        if ($this->existColumn('load', 'open_contacts_expiry')) {
            $this->dropColumn('load', 'open_contacts_expiry');
        }
        
        if ($this->existColumn('car_transporter', 'open_contacts_days')) {
            $this->dropColumn('car_transporter', 'open_contacts_days');
        }

        if ($this->existColumn('car_transporter', 'open_contacts_expiry')) {
            $this->dropColumn('car_transporter', 'open_contacts_expiry');
        }
        
        $this->delete('credit_services', [
            'credit_type' => CreditService::CREDIT_TYPE_OPEN_CONTACTS
        ]);
        
        $this->setAutoIncrement('credit_services', 3);
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
    
    /**
     * Checks whether column exists
     * 
     * @param string $table
     * @param integer $number
     */
    private function setAutoIncrement($table, $number)
    {
        $sql = "ALTER TABLE {$table} AUTO_INCREMENT = {$number};";
        Yii::$app->getDb()->createCommand($sql)->execute();
    }
}
