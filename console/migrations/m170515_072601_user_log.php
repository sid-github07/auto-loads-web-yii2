<?php

use common\models\User;
use common\models\UserLog;
use yii\db\Migration;

/**
 * Class m170515_072601_user_log
 */
class m170515_072601_user_log extends Migration
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

        $this->createTable(UserLog::tableName(), [
            'id' => $this->primaryKey()->comment('Įrašo ID'),
            'user_id' => $this->integer()->notNull()->comment('Vartotojo ID, kuris atliko veiksmą'),
            'action' => $this->string()->notNull()->comment('Vartotojo atlikto veiksmo tipas'),
            'data' => $this->text()->notNull()->comment('Vartotojo atlikto veiksmo duomenys'),
            'created_at' => $this->integer()->notNull()->comment('Įrašo sukūrimo data'),
            'updated_at' => $this->integer()->notNull()->comment('Įrašo paskutinio atnaujinimo data'),
        ], $tableOptions);

        $this->addForeignKey(
            'user_log_ibfk_1',
            UserLog::tableName(),
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
        $this->dropForeignKey('user_log_ibfk_1', UserLog::tableName());
        $this->dropTable(UserLog::tableName());
    }
}
