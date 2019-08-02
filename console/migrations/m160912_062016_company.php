<?php

use common\models\City;
use common\models\Company;
use common\models\User;
use yii\db\Migration;

class m160912_062016_company extends Migration
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

        $this->createTable(Company::tableName(), [
            'id' => $this->primaryKey()
                ->comment('Įrašo ID'),
            'owner_id' => $this->integer()
                ->notNull()
                ->comment('Vartotojo, sukūrusio įmonę, ID'),
            'title' => $this->string()
                ->defaultValue(Company::TITLE_DEFAULT_VALUE)
                ->comment('Įmonės pavadinimas'),
            'code' => $this->string()
                ->defaultValue(Company::CODE_DEFAULT_VALUE)
                ->comment('Įmonės kodas'),
            'vat_code' => $this->string()
                ->defaultValue(Company::VAT_CODE_DEFAULT_VALUE)
                ->comment('PVM kodas'),
            'address' => $this->string()
                ->notNull()
                ->comment('Vartotojo/įmonės adresas'),
            'city_id' => $this->integer()
                ->notNull()
                ->comment('Miestų lentelės ID'),
            'phone' => $this->string()
                ->notNull()
                ->comment('Vartotojo/įmonės telefono numeris'),
            'email' => $this->string()
                ->defaultValue(Company::EMAIL_DEFAULT_VALUE)
                ->comment('Įmonės el. paštas'),
            'website' => $this->string()
                ->defaultValue(Company::WEBSITE_DEFAULT_VALUE)
                ->comment('Įmonės internetinė svetainė'),
            'name' => $this->string()
                ->defaultValue(Company::NAME_DEFAULT_VALUE)
                ->comment('Vartotojo vardas'),
            'surname' => $this->string()
                ->defaultValue(Company::SURNAME_DEFAULT_VALUE)
                ->comment('Vartotojo pavardė'),
            'personal_code' => $this->string()
                ->defaultValue(Company::PERSONAL_CODE_DEFAULT_VALUE)
                ->comment('Vartotojo asmens kodas'),
            'active' => $this->boolean()
                ->notNull()
                ->comment('Požymis, ar įmonė aktyvi'),
            'allow' => $this->boolean()
                ->notNull()
                ->comment('Požymis, ar įmonei galima atlikti veiksmus'),
            'archive' => $this->boolean()
                ->notNull()
                ->comment('Požymis, ar įmonė archyvuota'),
            'visible' => $this->boolean()
                ->notNull()
                ->comment('Požymis, ar įmonė matoma'),
            'potential' => $this->boolean()
                ->notNull()
                ->defaultValue(Company::NOT_POTENTIAL)
                ->comment('Požymis ar įmonė potenciali'),
            'suggestions' => $this->boolean()
                ->notNull()
                ->defaultValue(Company::SEND_SUGGESTIONS)
                ->comment('Požymis, ar įmonės darbuotojams turi būti siunčiami krovinių pasiūlymai'),
            'created_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo sukūrimo data'),
            'updated_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo paskutinio atnaujinimo data'),
        ], $tableOptions);

        $this->addForeignKey(
            'company_ibfk_1',
            Company::tableName(),
            'owner_id',
            User::tableName(),
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'company_ibfk_2',
            Company::tableName(),
            'city_id',
            City::tableName(),
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
        $this->dropForeignKey('company_ibfk_1', Company::tableName());
        $this->dropForeignKey('company_ibfk_2', Company::tableName());
        $this->dropTable(Company::tableName());
    }
}
