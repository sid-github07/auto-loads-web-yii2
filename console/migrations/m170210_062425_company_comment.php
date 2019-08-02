<?php

use common\models\Admin;
use common\models\Company;
use common\models\CompanyComment;
use yii\db\Migration;

/**
 * Class m170210_062425_company_comment
 */
class m170210_062425_company_comment extends Migration
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

        $this->createTable(CompanyComment::tableName(), [
            'id' => $this->primaryKey()
                ->comment('Įrašo identifikacinis raktas'),
            'company_id' => $this->integer()
                ->notNull()
                ->comment('Įmonės ID, kuriai parašytas komentaras'),
            'admin_id' => $this->integer()
                ->notNull()
                ->comment('Administratoriaus ID, kuris parašė komentarą'),
            'comment' => $this->string(CompanyComment::COMMENT_MAX_LENGTH)
                ->notNull()
                ->comment('Komentaras apie įmonę'),
            'archived' => $this->smallInteger(1)
                ->notNull()
                ->defaultValue(0)
                ->comment('Požymis ar komentaras ištrintas'),
            'created_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo sukūrimo data'),
            'updated_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo paskutinio atnaujinimo data'),
        ], $tableOptions);
        
        $this->addForeignKey(
            'company_comment_ibfk_1',
            CompanyComment::tableName(),
            'admin_id',
            Admin::tableName(),
            'id',
            'RESTRICT',
            'CASCADE'
        );
        
        $this->addForeignKey(
            'company_comment_ibfk_2',
            CompanyComment::tableName(),
            'company_id',
            Company::tableName(),
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
        $this->dropForeignKey('company_comment_ibfk_1', CompanyComment::tableName());
        $this->dropForeignKey('company_comment_ibfk_2', CompanyComment::tableName());
        $this->dropTable(CompanyComment::tableName());
    }
}
