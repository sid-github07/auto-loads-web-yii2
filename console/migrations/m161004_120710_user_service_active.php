<?php

use common\models\Service;
use common\models\User;
use common\models\UserServiceActive;
use yii\db\Migration;

/**
 * Class m161004_120710_user_service_active
 */
class m161004_120710_user_service_active extends Migration
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

        $this->createTable(UserServiceActive::tableName(), [
            'id' => $this->primaryKey()
                ->comment('Įrašo ID'),
            'user_id' => $this->integer()
                ->notNull()
                ->comment('Vartotojo ID, kuris šiuo metu turi aktyvią paslaugą'),
            'service_id' => $this->integer()
                ->notNull()
                ->comment('Paslaugos ID, už kurią yra sumokėta'),
            'date_of_purchase' => $this->integer()
                ->notNull()
                ->comment('Data, kada paskutinį kartą mokėta už šio tipo paslaugą'),
            'status' => $this->smallInteger()
                ->notNull()
                ->comment('Paslaugos statusas'),
            'end_date' => $this->integer()
                ->notNull()
                ->comment('Data, iki kada galioja šio tipo paslauga'),
            'credits' => $this->integer()
                ->notNull()
                ->comment('Kreditų skaičius, kiek vartotojas gauna per mėnesį už šią paslaugą'),
            'reminder' => $this->boolean()
                ->defaultValue(UserServiceActive::DEFAULT_REMINDER)
                ->comment('Požymis, ar išsiųstas prenumeratos pasibaigimo priminimas'),
            'created_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo sukūrimo data'),
            'updated_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo paskutinio atnaujinimo data'),
        ], $tableOptions);

        $this->addForeignKey(
            'user_service_active_ibfk_1',
            UserServiceActive::tableName(),
            'user_id',
            User::tableName(),
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'user_service_active_ibfk_2',
            UserServiceActive::tableName(),
            'service_id',
            Service::tableName(),
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
        $this->dropTable(UserServiceActive::tableName());
    }
}
