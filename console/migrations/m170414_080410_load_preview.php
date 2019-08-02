<?php

use common\models\Load;
use common\models\LoadPreview;
use common\models\User;
use yii\db\Migration;

/**
 * Class m170414_080410_load_preview
 */
class m170414_080410_load_preview extends Migration
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

        $this->createTable(LoadPreview::tableName(), [
            'id' => $this->primaryKey()->comment('Įrašo ID'),
            'load_id' => $this->integer()->notNull()->comment('Krovinio ID'),
            'user_id' => $this->integer()->notNull()->comment('Vartotojo ID, kuris peržiūrėjo skelbimą'),
            'ip' => $this->string()->notNull()->comment('Vartotojo IP, kuris peržiūrėjo skelbimą'),
            'created_at' => $this->integer()->notNull()->comment('Įrašo sukūrimo data'),
            'updated_at' => $this->integer()->notNull()->comment('Įrašo paskutinio atnaujinimo data'),
        ], $tableOptions);

        $this->addForeignKey(
            'load_preview_ibfk_1',
            LoadPreview::tableName(),
            'load_id',
            Load::tableName(),
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'load_preview_ibfk_2',
            LoadPreview::tableName(),
            'user_id',
            User::tableName(),
            'id',
            'CASCADE', // Not sure
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('load_preview_ibfk_1', LoadPreview::tableName());
        $this->dropForeignKey('load_preview_ibfk_2', LoadPreview::tableName());
        $this->dropTable(LoadPreview::tableName());
    }
}
