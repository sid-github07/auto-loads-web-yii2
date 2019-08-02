<?php

use common\models\City;
use common\models\Load;
use common\models\LoadCity;
use yii\db\Migration;

/**
 * Class m161104_115108_load_city
 */
class m161104_115108_load_city extends Migration
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

        $this->createTable(LoadCity::tableName(), [
            'id' => $this->primaryKey()->comment('Įrašo ID'),
            'load_id' => $this->integer()->notNull()->comment('Krovinio ID'),
            'city_id' => $this->integer()->notNull()->comment('Miesto ID'),
            'load_postal_code' => $this->string(255)->defaultValue(null),
            'unload_postal_code' => $this->string(255)->defaultValue(null),
            'type' => $this->smallInteger()->notNull()->comment('Krovinio miesto tipas: pakrovimo arba iškrovimo'),
            'created_at' => $this->integer()->notNull()->comment('Įrašo sukūrimo data'),
            'updated_at' => $this->integer()->notNull()->comment('Įrašo paskutinio atnaujinimo data'),
        ], $tableOptions);

        $this->addForeignKey(
            'load_city_ibfk_1',
            LoadCity::tableName(),
            'load_id',
            Load::tableName(),
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'load_city_ibfk_2',
            LoadCity::tableName(),
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
        $this->dropForeignKey('load_city_ibfk_1', LoadCity::tableName());
        $this->dropForeignKey('load_city_ibfk_2', LoadCity::tableName());
        $this->dropTable(LoadCity::tableName());
    }
}
