<?php

use common\models\Admin;
use common\models\AdminAsUser;
use common\models\User;
use yii\db\Migration;

/**
 * Class m170912_084902_admin_as_user
 */
class m170912_084902_admin_as_user extends Migration
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

        $this->createTable(AdminAsUser::tableName(), [
            'id' => $this->primaryKey()->comment('Įrašo ID'),
            'admin_id' => $this->integer()
                ->notNull()
                ->comment('Administratoriaus ID'),
            'user_id' => $this->integer()
                ->notNull()
                ->comment('Vartotojo ID, prie kurio jungiasi administratorius'),
            'token' => $this->string()
                ->unique()
                ->null()
                ->comment('Unikalus kodas, administratoriui atpažinimui, atėjusiam per sugeneruotą nuorodą'),
            'ip' => $this->string()
                ->notNull()
                ->comment('Vartotojo IP, kuris peržiūrėjo skelbimą'),
            'created_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo sukūrimo data'),
            'updated_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo paskutinio atnaujinimo data'),
        ], $tableOptions);

        $this->addForeignKey(
            'admin_as_user_ibfk_1',
            AdminAsUser::tableName(),
            'admin_id',
            Admin::tableName(),
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'admin_as_user_ibfk_2',
            AdminAsUser::tableName(),
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
        $this->dropForeignKey('admin_as_user_ibfk_1', AdminAsUser::tableName());
        $this->dropForeignKey('admin_as_user_ibfk_2', AdminAsUser::tableName());
        $this->dropTable(AdminAsUser::tableName());
    }
}
