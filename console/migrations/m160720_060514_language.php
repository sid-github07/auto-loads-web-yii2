<?php

use common\models\Language;
use yii\db\Migration;

/**
 * Class m160720_060514_language
 */
class m160720_060514_language extends Migration
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

        $this->createTable(Language::tableName(), [
            'id' => $this->primaryKey(),
            /* TODO: pakeisti country_code ilgį (2) į konstantą */
            'country_code' => $this->string(2)->notNull(),
            'name' => $this->string()->notNull(),
        ], $tableOptions);

        /* TODO: pridėti foreign key ant country_code */
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(Language::tableName());
    }
}
