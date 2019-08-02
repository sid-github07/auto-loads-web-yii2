<?php

use common\models\CarTransporter;
use common\models\CarTransporterPreview;
use common\models\User;
use yii\db\Migration;

/**
 * Class m170919_083348_car_transporter_preview
 */
class m170919_083348_car_transporter_preview extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable(CarTransporterPreview::tableName(), [
            'id' => $this->primaryKey()->comment('Įrašo ID'),
            'car_transporter_id' => $this->integer()->notNull()->comment('Autovežio ID'),
            'user_id' => $this->integer()->notNull()->comment('Vartotojo ID, kuris peržiūrėjo skelbimą'),
            'ip' => $this->string()->notNull()->comment('Vartotojo IP, kuris peržiūrėjo skelbimą'),
            'created_at' => $this->integer()->notNull()->comment('Įrašo sukūrimo data'),
            'updated_at' => $this->integer()->notNull()->comment('Įrašo paskutinio atnaujinimo data'),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        $this->addForeignKey(
            'car_transporter_preview_ibfk_1',
            CarTransporterPreview::tableName(),
            'car_transporter_id',
            CarTransporter::tableName(),
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'car_transporter_preview_ibfk_2',
            CarTransporterPreview::tableName(),
            'user_id',
            User::tableName(),
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
        $this->dropForeignKey('car_transporter_preview_ibfk_1', CarTransporterPreview::tableName());
        $this->dropForeignKey('car_transporter_preview_ibfk_2', CarTransporterPreview::tableName());
        $this->dropTable(CarTransporterPreview::tableName());
    }
}
