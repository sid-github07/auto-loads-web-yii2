<?php

use common\models\CameFrom;
use common\models\Language;
use yii\db\Migration;

/**
 * Class m160722_080302_came_from
 */
class m160722_080302_came_from extends Migration
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

        $this->createTable(CameFrom::tableName(), [
            'id' => $this->primaryKey()->comment('Įrašo identifikacinis raktas'),
            'language_id' => $this->integer()->notNull()->comment('Kalbų lentelės identifikacinio rakto numeris'),
            'source_name' => $this->string()->notNull()->comment('"Iš kur atėjo vartotojas" pavadinimas'),
            'type' => $this->tinyInteger(1)->notNull()->defaultValue(CameFrom::TYPE_DEFAULT_VALUE),
            'created_at' => $this->integer()->notNull()->comment('Įrašo sukūrimo data'),
            'updated_at' => $this->integer()->notNull()->comment('Įrašo paskutinio atnaujinimo data'),
        ], $tableOptions);

        $this->addForeignKey(
            'came_from_ibfk_1',
            CameFrom::tableName(),
            'language_id',
            Language::tableName(),
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
        $this->dropForeignKey('came_from_ibfk_1', CameFrom::tableName());
        $this->dropTable(CameFrom::tableName());
    }
}
