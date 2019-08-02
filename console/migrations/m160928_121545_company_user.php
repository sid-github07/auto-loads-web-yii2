<?php

use common\models\Company;
use common\models\CompanyUser;
use common\models\User;
use yii\db\Migration;

/**
 * Class m160928_121545_company_user
 */
class m160928_121545_company_user extends Migration
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

        $this->createTable(CompanyUser::tableName(), [
            'company_id' => $this->integer()->notNull()->comment('Įmonės ID'),
            'user_id' => $this->integer()->unique()->notNull()->comment('Vartotojo ID')
        ], $tableOptions);

        $this->addForeignKey(
            'company_user_ibfk_1',
            CompanyUser::tableName(),
            'company_id',
            Company::tableName(),
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'company_user_ibfk_2',
            CompanyUser::tableName(),
            'user_id',
            User::tableName(),
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('company_user_ibfk_1', CompanyUser::tableName());
        $this->dropForeignKey('company_user_ibfk_2', CompanyUser::tableName());
        $this->dropTable(CompanyUser::tableName());
    }
}
