<?php

use common\models\FaqFeedback;
use yii\db\Migration;

/**
 * Class m160712_061250_faq_feedback
 */
class m160712_061250_faq_feedback extends Migration
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

        $this->createTable(FaqFeedback::tableName(), [
            'id' => $this->primaryKey()->comment('Įrašo identifikacinis raktas'),
            'question' => $this->string()->notNull()->comment('Klausimo "placeholder" vertimų faile'),
            'email' => $this->string()->notNull()->comment('Kliento, kuris užduoda klausimą, el. paštas'),
            'comment' => $this->text()->notNull()->comment('Kliento klausimas/komentaras'),
            'solved' => $this->boolean()
                             ->defaultValue(FaqFeedback::DEFAULT_SOLVED_VALUE)
                             ->notNull()
                             ->comment('Požymis, ar klausimas išspręstas/atsakytas'),
            'created_at' => $this->integer()->notNull()->comment('Įrašo sukūrimo data'),
            'updated_at' => $this->integer()->notNull()->comment('Įrašo paskutinio atnaujinimo data'),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(FaqFeedback::tableName());
    }
}
