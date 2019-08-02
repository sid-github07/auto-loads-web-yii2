<?php

use common\models\User;
use yii\db\Migration;

/**
 * Class m160718_101836_user
 */
class m160718_101836_user extends Migration
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

        $this->createTable(User::tableName(), [
            'id' => $this->primaryKey()
                ->comment('Įrašo identifikacinis raktas'),
            'name' => $this->string()
                ->notNull()
                ->comment('Vartotojo vardas'),
            'surname' => $this->string()
                ->notNull()
                ->comment('Vartotojo pavardė'),
            'email' => $this->string()
                ->unique()
                ->notNull()
                ->comment('Vartotojo el. paštas'),
            'phone' => $this->string()
                ->notNull()
                ->comment('Vartotojo telefono numeris'),
            'auth_key' => $this->string(User::AUTH_KEY_MAX_LENGTH)
                ->notNull()
                ->comment('"Prisiminti mane" autentifikacinis raktas'),
            'password_hash' => $this->string()
                ->notNull()
                ->comment('Slaptažodžio "hash"'),
            'password_reset_token' => $this->string()
                ->unique()
                ->defaultValue(User::DEFAULT_PASSWORD_RESET_TOKEN)
                ->comment('Slaptažodžio priminimo "token"'),
            'password_expires' => $this->integer()
                ->notNull()
                ->comment('Data, kada pasibaigs slaptažodis'),
            'class' => $this->smallInteger()
                ->defaultValue(User::DEFAULT_CLASS)
                ->comment('Veiklos tipas (vežėjas, tiekėjas)'),
            'original_class' => $this->smallInteger()
                ->defaultValue(User::DEFAULT_ORIGINAL_CLASS)
                ->comment('Vartotojo pasirinktas veiklos tipas'),
            'account_type' => $this->smallInteger()
                ->defaultValue(User::DEFAULT_ACCOUNT_TYPE)
                ->comment('Asmens tipas (fizinis, juridinis)'),
            'personal_code' => $this->string()
                ->defaultValue(User::DEFAULT_PERSONAL_CODE)
                ->comment('Asmens kodas'),
            'company_code' => $this->string()
                ->unique()
                ->defaultValue(User::DEFAULT_PERSONAL_CODE)
                ->comment('Įmonės kodas'),
            'company_name' => $this->string()
                ->unique()
                ->defaultValue(User::DEFAULT_COMPANY_NAME)
                ->comment('Įmonės pavadinimas'),
            'came_from_referer' => $this->text()
                ->defaultValue(null),
            'city_id' => $this->integer()
                ->defaultValue(User::DEFAULT_CITY_ID)
                ->comment('Miestų lentelės identifikacinio rakto numeris'),
            'address' => $this->string(User::ADDRESS_MAX_LENGTH)
                ->defaultValue(User::DEFAULT_ADDRESS)
                ->comment('Vartotojo/įmonės adresas'),
            'vat_code' => $this->string()
                ->defaultValue(User::DEFAULT_VAT_CODE)
                ->comment('PVM kodas'),
            'came_from_id' => $this->integer()
                ->defaultValue(User::DEFAULT_CAME_FROM_ID)
                ->comment('"Iš kur sužinojote apie mus" lentelės identifikacinis rakto numeris'),
            'current_credits' => $this->integer()
                ->defaultValue(User::DEFAULT_CURRENT_CREDITS)
                ->comment('Kiek vartotojas šiuo metu turi kreditų'),
            'active' => $this->boolean()
                ->notNull()
                ->comment('Požymis, ar vartotojas aktyvus'),
            'allow' => $this->boolean()
                ->notNull()
                ->comment('Požymis, ar vartotojas gali prisijungti'),
            'archive' => $this->boolean()
                ->notNull()
                ->comment('Požymis, ar vartotojo paskyra archyvuota'),
            'visible' => $this->boolean()
                ->notNull()
                ->comment('Požymis, ar vartotojo paskyra matoma'),
            'suggestions' => $this->tinyInteger()
                ->defaultValue(1)
                ->notNull(),
            'suggestions_token' => $this->string(255)
                ->notNull(),
            'last_login' => $this->integer()
                ->notNull()
                ->comment('Vartotojo paskutinio prisijungimo data'),
            'warning_sent' => $this->integer()
                ->defaultValue(User::DEFAULT_WARNING_SENT)
                ->comment('Data, kada išsiųstas įspėjimas'),
            'blocked_until' => $this->integer()
                ->defaultValue(User::DEFAULT_BLOCKED_UNTIL)
                ->comment('Data, iki kada vartotojas užblokuotas'),
            'token' => $this->string()
                ->unique()
                ->defaultValue(User::DEFAULT_TOKEN)
                ->comment('Unikalus kodas, vartotojo atpažinimui, atėjusiam per sugeneruotą nuorodą'),
            'created_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo sukūrimo data'),
            'updated_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo paskutinio atnaujinimo data'),
        ], $tableOptions);
        
        $this->createIndex('came_from_id', User::tableName(), 'came_from_id');
        $this->createIndex('city_id', User::tableName(), 'city_id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropIndex('came_from_id', User::tableName());
        $this->dropIndex('city_id', User::tableName());
        $this->dropTable(User::tableName());
    }
}
