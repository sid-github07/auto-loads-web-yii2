<?php

use common\models\Load;
use common\models\LoadSuggestion;
use yii\db\Migration;

/**
 * Class m180327_120603_load_suggestion
 */
class m180327_120603_load_suggestion extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(LoadSuggestion::tableName(), [
            'id' => $this->primaryKey()
                ->comment('Įrašo ID'),
            'user_id' => $this->integer()
                ->notNull()
                ->comment('Vartotojo ID, kuriam išsiųstas laiškas'),
            'search_radius' => $this->integer()
                ->notNull()
                ->defaultValue(Load::THIRD_RADIUS)
                ->comment('Paieškos spindulys'),
            'date' => $this->integer()
                ->notNull()
                ->defaultValue(0)
                ->comment('Paieškos data'),
            'quantity' => $this->integer()
                ->notNull()
                ->defaultValue(1)
                ->comment('Ieškotas kiekis'),
            'token' => $this->string()
                ->notNull()
                ->comment('Atpažinimo ženkliukas'),
            'load' => $this->integer()
                ->notNull()
                ->comment('Pakrovimo miesto id'),
            'unload' => $this->integer()
                ->notNull()
                ->comment('Iškrovimo miesto id'),
            'created_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo sukūrimo data'),
            'updated_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo paskutinio atnaujinimo data'),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable(LoadSuggestion::tableName());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180327_120603_load_suggestion cannot be reverted.\n";

        return false;
    }
    */
}
