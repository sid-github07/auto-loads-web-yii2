<?php

use common\models\CountryPhoneCode;
use yii\db\Migration;

/**
 * Class m160719_080235_country_phone_code
 */
class m160719_080235_country_phone_code extends Migration
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

        $this->createTable(CountryPhoneCode::tableName(), [
            'id' => $this->primaryKey(),
            /* TODO: pakeisti country_code ilgį (2) į konstantą */
            'country_code' => $this->string(2)->notNull(),
            'name' => $this->string()->notNull(),
            'number' => $this->string()->notNull(),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(CountryPhoneCode::tableName());
    }
}
