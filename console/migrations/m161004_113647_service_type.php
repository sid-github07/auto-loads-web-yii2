<?php

use common\models\ServiceType;
use yii\db\Migration;

/**
 * Class m161004_113647_service_type
 */
class m161004_113647_service_type extends Migration
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

        $this->createTable(ServiceType::tableName(), [
            'id' => $this->primaryKey()
                ->comment('Įrašo ID'),
            'name' => $this->string()
                ->notNull()
                ->comment('Paslaugos tipo pavadinimas'),
            'order_by_user' => $this->boolean()
                ->notNull()
                ->comment('Požymis, ar vartotojas gali pats užsisakyti šio tipo paslaugą'),
            'created_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo sukūrimo data'),
            'updated_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo paskutinio atnaujinimo data'),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(ServiceType::tableName());
    }
}
