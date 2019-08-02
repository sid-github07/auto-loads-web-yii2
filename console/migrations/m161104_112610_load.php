<?php

use common\models\Load;
use common\models\User;
use yii\db\Migration;

/**
 * Class m161104_112610_load
 */
class m161104_112610_load extends Migration
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

        $this->createTable(Load::tableName(), [
            'id' => $this->primaryKey()
                ->comment('Įrašo ID'),
            'user_id' => $this->integer()
                ->null()
                ->defaultValue(Load::DEFAULT_USER_ID)
                ->comment('Vartotojo ID, kuris sukūrė skelbimą'),
            'code' => $this->string()
                ->unique()
                ->defaultValue(Load::DEFAULT_CODE)
                ->comment('Unikalus krovinio kodas'),
            'type' => $this->smallInteger()
                ->notNull()
                ->comment('Krovinio tipas: pilnas arba dalinis autovežio užkrovimas'),
            'payment_method' => $this->smallInteger()
                ->notNull()
                ->comment('Apmokėjimo būdas: už visą krovinį arba už pervežtą vienetą'),
            'date' => $this->integer()
                ->notNull()
                ->comment('Pakrovimo data'),
            'price' => $this->decimal(Load::PRICE_PRECISION, Load::PRICE_SCALE)
                ->defaultValue(Load::DEFAULT_PRICE)
                ->comment('Krovinio kaina, jeigu pasirinko, jog apmokės už visą krovinį'),
            'status' => $this->smallInteger()
                ->notNull()
                ->comment('Krovinio statusas'),
            'active' => $this->boolean()
                ->defaultValue(Load::DEFAULT_ACTIVE)
                ->comment('Požymis ar krovinys buvo aktyvuotas per 24 val.'),
            'date_of_expiry' => $this->integer()
                ->notNull()
                ->comment('Data, iki kada galioja krovinio skelbimas'),
            'token' => $this->string()
                ->unique()
                ->defaultValue(Load::DEFAULT_TOKEN)
                ->comment('Identifikacinis krovinio skelbimo kodas'),
            'phone' => $this->string()
                ->defaultValue(Load::DEFAULT_PHONE)
                ->comment('Skelbėjo telefono numeris'),
            'email' => $this->string()
                ->defaultValue(Load::DEFAULT_EMAIL)
                ->comment('Skelbėjo el. pašto adresas'),
            'created_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo sukūrimo data'),
            'updated_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo paskutinio atnaujinimo data'),
        ], $tableOptions);

        $this->addForeignKey(
            'load_ibfk_1',
            Load::tableName(),
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
        $this->dropForeignKey('load_ibfk_1', Load::tableName());
        $this->dropTable(Load::tableName());
    }
}
