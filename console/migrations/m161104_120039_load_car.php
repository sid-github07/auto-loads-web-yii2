<?php

use common\models\Load;
use common\models\LoadCar;
use yii\db\Migration;

/**
 * Class m161104_120039_load_car
 */
class m161104_120039_load_car extends Migration
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

        $this->createTable(LoadCar::tableName(), [
            'id' => $this->primaryKey()
                ->comment('Įrašo ID'),
            'load_id' => $this->integer()
                ->notNull()
                ->comment('Krovinio ID'),
            'quantity' => $this->smallInteger()
                ->defaultValue(LoadCar::DEFAULT_QUANTITY)
                ->comment('Krovinio automobilių kiekis'),
            'model' => $this->string(LoadCar::MODEL_MAX_LENGTH)
                ->defaultValue(LoadCar::DEFAULT_MODEL)
                ->comment('Automobilio modelis'),
            'price' => $this->decimal(LoadCar::PRICE_PRECISION, LoadCar::PRICE_SCALE)
                ->defaultValue(LoadCar::DEFAULT_PRICE)
                ->comment('Kaina už konkretaus tipo automobilio pervežimą'),
            'state' => $this->smallInteger()
                ->defaultValue(LoadCar::DEFAULT_STATE)
                ->comment('Automobilio būklė: naujas, naudotas, nevažiuojantis'),
            'created_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo sukūrimo data'),
            'updated_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo paskutinio atnaujinimo data'),
        ], $tableOptions);

        $this->addForeignKey(
            'load_car_ibfk_1',
            LoadCar::tableName(),
            'load_id',
            Load::tableName(),
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
        $this->dropForeignKey('load_car_ibfk_1', LoadCar::tableName());
        $this->dropTable(LoadCar::tableName());
    }
}
