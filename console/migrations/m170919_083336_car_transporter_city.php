<?php

use common\models\CarTransporter;
use common\models\CarTransporterCity;
use common\models\City;
use common\models\Country;
use yii\db\Migration;

/**
 * Class m170919_083336_car_transporter_city
 */
class m170919_083336_car_transporter_city extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable(CarTransporterCity::tableName(), [
            'id' => $this->primaryKey()->comment('Įrašo ID'),
            'car_transporter_id' => $this->integer()->notNull()->comment('Autovežio ID'),
            'city_id' => $this->integer()->notNull()->comment('Miesto ID'),
            'load_postal_code' => $this->string(255)->defaultValue(null),
            'unload_postal_code' => $this->string(255)->defaultValue(null),
            'country_code' => $this->string()->notNull()->comment('Šalies kodas, kuriam priklauso miestas'),
            'type' => $this->smallInteger()->notNull()->comment('Autovežio miesto tipas: pakrovimo arba iškrovimo'),
            'created_at' => $this->integer()->notNull()->comment('Įrašo sukūrimo data'),
            'updated_at' => $this->integer()->notNull()->comment('Įrašo paskutinio atnaujinimo data'),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        $this->addForeignKey(
            'car_transporter_city_ibfk_1',
            CarTransporterCity::tableName(),
            'car_transporter_id',
            CarTransporter::tableName(),
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'car_transporter_city_ibfk_2',
            CarTransporterCity::tableName(),
            'city_id',
            City::tableName(),
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'car_transporter_city_ibfk_3',
            CarTransporterCity::tableName(),
            'country_code',
            Country::tableName(),
            'code',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('car_transporter_city_ibfk_1', CarTransporterCity::tableName());
        $this->dropForeignKey('car_transporter_city_ibfk_2', CarTransporterCity::tableName());
        $this->dropForeignKey('car_transporter_city_ibfk_3', CarTransporterCity::tableName());
        $this->dropTable(CarTransporterCity::tableName());
    }
}
