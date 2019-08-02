<?php

use common\models\Admin;
use common\models\Service;
use common\models\User;
use common\models\UserService;
use yii\db\Migration;

/**
 * Class m161004_115549_user_service
 */
class m161004_115549_user_service extends Migration
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

        $this->createTable(UserService::tableName(), [
            'id' => $this->primaryKey()
                ->comment('Įrašo ID'),
            'old_id' => $this->integer()
                ->defaultValue(null),
            'user_id' => $this->integer()
                ->notNull()
                ->comment('Vartotojo ID, kuriam priskiriama paslauga'),
            'service_id' => $this->integer()
                ->notNull()
                ->comment('Paslaugos ID, kuri priskiriama vartotojui'),
            'paid' => $this->boolean()
                ->notNull()
                ->comment('Požymis, ar paslauga apmokėta'),
            'paid_by' => $this->smallInteger()
                ->defaultValue(UserService::DEFAULT_PAID_BY)
                ->comment('Požymis, kas apmokėjo už paslaugą'),
            'admin_id' => $this->integer()
                ->defaultValue(UserService::DEFAULT_ADMIN_ID)
                ->comment('Administratoriaus ID, kuris suteikė paslaugą arba null, jeigu paslaugą nusipirko pats vartotojas'),
            'generated_by' => $this->integer()
                ->null()
                ->defaultValue(UserService::DEFAULT_GENERATED_BY)
                ->comment('Administratoriaus ID, kuris sugeneravo sąskaitą arba null, jeigu sąskaitą sugeneravo vartotojas'),
            'start_date' => $this->integer()
                ->defaultValue(UserService::DEFAULT_START_DATE)
                ->comment('Data, kada sumokėta už paslaugą'),
            'end_date' => $this->integer()
                ->defaultValue(UserService::DEFAULT_END_DATE)
                ->comment('Data, iki kada galioja paslauga'),
            'price' => $this->decimal(UserService::PRICE_PRECISION, UserService::PRICE_SCALE)
                ->notNull()
                ->comment('Paslaugos kaina'),
            'response' => $this->text()
                ->defaultValue(UserService::DEFAULT_RESPONSE)
                ->comment('Mokėjimo atsakymas'),
            'created_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo sukūrimo data'),
            'updated_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo paskutinio atnaujinimo data'),
        ], $tableOptions);

        $this->addForeignKey(
            'user_service_ibfk_1',
            UserService::tableName(),
            'user_id',
            User::tableName(),
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'user_service_ibfk_2',
            UserService::tableName(),
            'service_id',
            Service::tableName(),
            'id',
            'RESTRICT',
            'CASCADE'
        );
        
        $this->addForeignKey(
            'user_service_ibfk_3',
            UserService::tableName(),
            'admin_id',
            Admin::tableName(),
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'user_service_ibfk_4',
            UserService::tableName(),
            'generated_by',
            Admin::tableName(),
            'id',
            'RESTRICT',
            'CASCADE'
        );
        
        $this->createIndex('generated_by', UserService::tableName(), 'generated_by');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropIndex('generated_by', UserService::tableName());
        $this->dropForeignKey('user_service_ibfk_1', UserService::tableName());
        $this->dropForeignKey('user_service_ibfk_2', UserService::tableName());
        $this->dropForeignKey('user_service_ibfk_3', UserService::tableName());
        $this->dropForeignKey('user_service_ibfk_4', UserService::tableName());
        $this->dropTable(UserService::tableName());
    }
}
