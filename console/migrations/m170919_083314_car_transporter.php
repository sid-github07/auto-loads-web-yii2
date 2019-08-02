<?php

use common\models\CarTransporter;
use common\models\User;
use yii\db\Migration;

/**
 * Class m170919_083314_car_transporter
 */
class m170919_083314_car_transporter extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable(CarTransporter::tableName(), [
            'id' => $this->primaryKey()
                ->comment('Įrašo ID'),
            'user_id' => $this->integer()
                ->notNull()
                ->comment('Vartotojo ID, kuris sukūrė skelbimą'),
            'code' => $this->string()
                ->null()
                ->unique()
                ->defaultValue(CarTransporter::CODE_DEFAULT_VALUE)
                ->comment('Unikalus autovežio kodas'),
            'quantity' => $this->smallInteger()
                ->null()
                ->defaultValue(CarTransporter::QUANTITY_DEFAULT_VALUE)
                ->comment('Laisvų vietų kiekis autovežyje'),
            'available_from' => $this->integer()
                ->null()
                ->defaultValue(CarTransporter::AVAILABLE_FROM_DEFAULT_VALUE)
                ->comment('Data nuo kada laisvas autovežis'),
            'date_of_expiry' => $this->integer()
                ->notNull()
                ->comment('Data, iki kada galioja autovežio skelbimas'),
            'visible' => $this->smallInteger()
                ->notNull()
                ->comment('Požymis, ar autovežio skelbimas rodomas'),
            'archived' => $this->smallInteger()
                ->notNull()
                ->comment('Požymis, ar autovežio skelbimas archyvuotas'),
            'created_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo sukūrimo data'),
            'updated_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo paskutinio atnaujinimo data'),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        $this->addForeignKey(
            'car_transporter_ibfk_1',
            CarTransporter::tableName(),
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
        $this->dropForeignKey('car_transporter_ibfk_1', CarTransporter::tableName());
        $this->dropTable(CarTransporter::tableName());
    }
}
