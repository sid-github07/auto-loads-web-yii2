<?php

use common\models\Service;
use common\models\ServiceType;
use yii\db\Migration;

/**
 * Class m161004_114620_service
 */
class m161004_114620_service extends Migration
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

        $this->createTable(Service::tableName(), [
            'id' => $this->primaryKey()
                ->comment('Įrašo ID'),
            'service_type_id' => $this->integer()
                ->notNull()
                ->comment('Paslaugos tipo ID, kuriai priklauso ši paslauga'),
            'days' => $this->integer()
                ->notNull()
                ->comment('Dienų skaičius, kiek galioja paslauga'),
            'price' => $this->decimal(10, 2)
                ->notNull()
                ->comment('Paslaugos kaina'),
            'name' => $this->string()
                ->notNull()
                ->comment('Paslaugos pavadinimas'),
            'credits' => $this->integer()
                ->notNull()
                ->comment('Kreditų skaičius, kiek vartotojas gauna per mėnesį už šią paslaugą'),
            'created_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo sukūrimo data'),
            'updated_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo paskutinio atnaujinimo data'),
        ], $tableOptions);

        $this->addForeignKey(
            'service_ibfk_1',
            Service::tableName(),
            'service_type_id',
            ServiceType::tableName(),
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
        $this->dropTable(Service::tableName());
    }
}
