<?php

use common\models\City;
use yii\db\Migration;

/**
 * Class m160721_052734_city
 */
class m160721_052734_city extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(City::tableName(), [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'ansi_name' => $this->string()->notNull(),
            'alt_name' => $this->text()->notNull(),
            'latitude' => $this->decimal(10, 7)->defaultValue(null),
            'longitude' => $this->decimal(10, 7)->defaultValue(null),
            /* TODO: pakeisti country_code ilgį (2) į konstantą */
            'country_code' => $this->string(2)->defaultValue(null),
            'population' => $this->integer()->defaultValue(null),
            'elevation' => $this->integer()->defaultValue(null),
            'timezone' => $this->string()->defaultValue(null),
            'modification_date' => $this->date()->defaultValue(null),
        ], $tableOptions);
    
        $this->createIndex('country_code', City::tableName(), 'country_code');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropIndex('country_code', City::tableName());
        $this->dropTable(City::tableName());
    }
}
