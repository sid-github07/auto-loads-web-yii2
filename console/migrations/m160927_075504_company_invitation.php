<?php

use common\models\Company;
use common\models\CompanyInvitation;
use yii\db\Migration;

/**
 * Class m160927_075504_company_invitation
 */
class m160927_075504_company_invitation extends Migration
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

        $this->createTable(CompanyInvitation::tableName(), [
            'id' => $this->primaryKey()
                ->comment('Įrašo ID'),
            'company_id' => $this->integer()
                ->notNull()
                ->comment('Įmonės ID, kuri siunčia pakvietimą'),
            'email' => $this->string()
                ->notNull()
                ->comment('Vartotojo el. paštas, kuriam sunčiamas pakvietimas'),
            'token' => $this->string(CompanyInvitation::TOKEN_MAX_LENGTH)
                ->defaultValue(CompanyInvitation::DEFAULT_TOKEN_VALUE)
                ->unique()
                ->comment('Unikalus kodas, vartotojo atpažinimui, atėjusįam per sugeneruotą nuorodą'),
            'accepted' => $this->boolean()
                ->notNull()
                ->comment('Požymis, ar pakvietimas buvo priimtas'),
            'created_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo sukūrimo data'),
            'updated_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo paskutinio atnaujinimo data'),
        ], $tableOptions);

        $this->addForeignKey(
            'company_invitation_ibfk_1',
            CompanyInvitation::tableName(),
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
        $this->dropForeignKey('company_invitation_ibfk_1', CompanyInvitation::tableName());
        $this->dropTable(CompanyInvitation::tableName());
    }
}
