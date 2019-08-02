<?php

use common\models\Country;
use yii\db\Migration;

/**
 * Class m160719_073714_country
 */
class m160719_073714_country extends Migration
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

        $this->createTable(Country::tableName(), [
            'code' => $this->string(Country::CODE_LENGTH)->unique()->notNull(),
            'name' => $this->string()->notNull(),
            'vat_rate' => $this->decimal(5,2)->defaultValue(null),
        ], $tableOptions);

        $this->addPrimaryKey('country_code', Country::tableName(), 'code');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(Country::tableName());
    }
}
