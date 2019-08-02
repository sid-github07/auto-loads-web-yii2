<?php

use common\models\Admin;
use yii\db\Migration;

/**
 * Class m161000_130438_admin
 */
class m161000_130438_admin extends Migration
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

        $this->createTable(Admin::tableName(), [ 
            'id' => $this->primaryKey()
                ->comment('Įrašo identifikacinis raktas'),
            'name' => $this->string()
                ->notNull()
                ->comment('Administratoriaus vardas'),
            'surname' => $this->string()
                ->notNull()
                ->comment('Administratoriaus pavardė'),
            'email' => $this->string()
                ->unique()
                ->notNull()
                ->comment('Administratoriaus el. paštas'),
            'phone' => $this->string()
                ->comment('Administratoriaus telefono numeris'),
            'auth_key' => $this->string(Admin::AUTH_KEY_MAX_LENGTH)
                ->comment('"Prisiminti mane" autentifikacinis raktas'),
            'password_hash' => $this->string()
                ->notNull()
                ->comment('Slaptažodžio "hash"'),
            'password_reset_token' => $this->string()
                ->unique()
                ->defaultValue(Admin::DEFAULT_PASSWORD_RESET_TOKEN)
                ->comment('Slaptažodžio priminimo "token"'),
            'admin' => $this->smallInteger()
                ->notNull()
                ->comment('Administratoriaus rolė'),
            'archived' => $this->boolean()
                ->defaultValue(Admin::DEFAULT_ACCOUNT_STATUS)
                ->comment('Administratoriaus paskyros statusas'),
            'created_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo sukūrimo data'),
            'updated_at' => $this->integer()
                ->notNull()
                ->comment('Įrašo paskutinio atnaujinimo data'),
        ], $tableOptions);
    }

    /**
     * 
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(Admin::tableName());
    }
}
