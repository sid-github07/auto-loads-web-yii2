<?php

use common\models\LoadSuggestionCity;
use yii\db\Migration;

/**
 * Class m180327_140611_load_suggestion_city
 */
class m180327_140611_load_suggestion_city extends Migration
{
    /**
     * {@inheritdoc}
     */
//    public function safeUp()
//    {
//
//    }

    /**
     * {@inheritdoc}
     */
//    public function safeDown()
//    {
//        echo "m180327_140611_load_suggestion_city cannot be reverted.\n";
//
//        return false;
//    }


    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable(LoadSuggestionCity::tableName(), [
            'id' => $this->primaryKey()
                ->comment('Įrašo ID'),
            'load_id' => $this->integer()
                ->notNull()
                ->comment('Pervežimo ID'),
            'user_id' => $this->integer()
                ->notNull()
                ->comment('Vartotojo ID, kuriam išsiųstas laiškas'),
            'token' => $this->string()
                ->notNull()
                ->comment('Atpažinimo ženkliukas'),
            'created_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo sukūrimo data'),
            'updated_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo paskutinio atnaujinimo data'),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
    }

    public function down()
    {
        $this->dropTable(LoadSuggestionCity::tableName());
    }
}
