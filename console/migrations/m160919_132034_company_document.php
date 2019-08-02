<?php

use common\models\Company;
use common\models\CompanyDocument;
use yii\db\Migration;

/**
 * Class m160919_132034_company_document
 */
class m160919_132034_company_document extends Migration
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

        $this->createTable(CompanyDocument::tableName(), [
            'id' => $this->primaryKey()->comment('Įrašo ID'),
            'company_id' => $this->integer()->notNull()->comment('Įmonės ID'),
            'date' => $this->integer()->notNull()->comment('Dokumento galiojimo data'),
            'type' => $this->smallInteger()->notNull()->comment('Dokumento tipas'),
            'extension' => $this->string()->notNull()->comment('Dokumento plėtinys'),
            'created_at' => $this->integer()->notNull()->comment('Įrašo sukūrimo data'),
            'updated_at' => $this->integer()->notNull()->comment('Įrašo paskutinio atnaujinimo data'),
        ], $tableOptions);

        $this->addForeignKey(
            'company_document_ibfk_1',
            CompanyDocument::tableName(),
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
        $this->dropForeignKey('company_document_ibfk_1', CompanyDocument::tableName());
        $this->dropTable(CompanyDocument::tableName());
    }
}
