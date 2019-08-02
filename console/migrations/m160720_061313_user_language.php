<?php

use common\models\Language;
use common\models\User;
use common\models\UserLanguage;
use yii\db\Migration;

/**
 * Class m160720_061313_user_language
 */
class m160720_061313_user_language extends Migration
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

        $this->createTable(UserLanguage::tableName(), [
            'user_id' => $this->integer()->notNull(),
            'language_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'user_language_ibfk_1',
            UserLanguage::tableName(),
            'user_id',
            User::tableName(),
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'user_language_ibfk_2',
            UserLanguage::tableName(),
            'language_id',
            Language::tableName(),
            'id',
            'RESTRICT',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(UserLanguage::tableName());
    }
}
