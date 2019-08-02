<?php

use yii\db\Migration;

/**
 * Class m180914_084506_create_user_data_for_dev_environment
 */
class m180914_084506_create_user_data_for_dev_environment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (YII_DEBUG === true) {
            $user = \common\models\User::find()->where(['email' => 'kasp.mindaugas@gmail.com'])->one();
            if (is_null($user)) {
                echo "Inserting user with email kasp.mindaugas@gmail.com\n";
                $this->insert(\common\models\User::tableName(), [
                    'id' => 1,
                    'name' => 'Mindaugas',
                    'surname' => 'Kasparavičius',
                    'email' => 'kasp.mindaugas@gmail.com',
                    'phone' => '+37067819938',
                    'auth_key' => 'H68_MpuzvqC5nScuEAgYpGnY0OEftP80',
                    'password_hash' => '$2y$13$kcm4PMXsZAKGFyNPKNbl2.u26SU4cxKgrBiisThukx5Loa8M16e1.',
                    'password_reset_token' => 'fyyMpBoIDsZSH1R42vkKuetSFJ8sl6RR_1531146874',
                    'password_expires' => '1568450576',
                    'class' => 0,
                    'original_class' => 1,
                    'account_type' => 0,
                    'came_from_referer' => 'https://lt.dev.auto-loads.com/prenumerata/active-services',
                    'city_id' => 598316,
                    'address' => 'Kaunas',
                    'came_from_id' => 91,
                    'active' => 1,
                    'allow' => 1,
                    'archive' => 0,
                    'visible' => 1,
                    'suggestions' => 1,
                    'suggestions_token' => '3nIqypfEX0-xXeBvhne5DeGxGZl6fL1y',
                    'last_login' => '1536914576',
                    'service_credits' => 0,
                    'current_credits' => 0,
                    'created_at' => time(),
                    'updated_at' => time(),
                ]);
                $this->insert(\common\models\Company::tableName(), [
                    'owner_id' => 1,
                    'address' => 'Kaunas',
                    'city_id' => 598316,
                    'phone' => '+37067819938',
                    'email' => 'kasp.mindaugas@gmail.com',
                    'name' => 'Mindaugas',
                    'surname' => 'Kasparavičius',
                    'active' => 1,
                    'allow' => 1,
                    'archive' => 0,
                    'visible' => 1,
                    'created_at' => time(),
                    'updated_at' => time(),
                ]);
            }

            $user = \common\models\User::find()->where(['email' => 'simonas@auto-loads.com'])->one();
            if (is_null($user)) {
                echo "Inserting user with email simonas@auto-loads.com\n'";
                $this->insert(\common\models\User::tableName(), [
                    'id' => 2,
                    'name' => 'Simonas',
                    'surname' => 'Niedvaras',
                    'email' => 'simonas@auto-loads.com',
                    'phone' => '+37067022975',
                    'auth_key' => 'Krdohap7RjB_KBXGbsffd30n5F_hnHzA',
                    'password_hash' => '$2y$13$mlFgTMYfySCpJg2HDC9E..Jo63XwKN7OXeB6pE.N2P7lNlT4jjnQS',
                    'password_reset_token' => 'ct5euoBdUwjflsb_TwdNVIdNU5NRCuIT_1533645171',
                    'password_expires' => '1565682960',
                    'class' => 2,
                    'original_class' => 2,
                    'account_type' => 0,
                    'came_from_referer' => 'https://lt.dev.auto-loads.com/prenumerata/active-services',
                    'city_id' => 598316,
                    'address' => 'Kaunas',
                    'came_from_id' => 91,
                    'active' => 1,
                    'allow' => 1,
                    'archive' => 0,
                    'visible' => 1,
                    'suggestions' => 1,
                    'suggestions_token' => 'U-0XIMyrYK6BN2uI0KsBsdgGDfau7BED',
                    'last_login' => '1536914576',
                    'service_credits' => 0,
                    'current_credits' => 0,
                    'created_at' => time(),
                    'updated_at' => time(),
                ]);
                $this->insert(\common\models\Company::tableName(), [
                    'owner_id' => 2,
                    'address' => 'Kaunas',
                    'city_id' => 598316,
                    'phone' => '+37067022975',
                    'email' => 'simonas@auto-loads.com',
                    'name' => 'Simonas',
                    'surname' => 'Niedvaras',
                    'active' => 1,
                    'allow' => 1,
                    'archive' => 0,
                    'visible' => 1,
                    'created_at' => time(),
                    'updated_at' => time(),
                ]);
            }
        } else {
            echo 'This migration can only run in DEBUG mode. [YII_DEBUG]\n';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if (YII_DEBUG === true) {
            $user = \common\models\User::find()->where(['email' => 'kasp.mindaugas@gmail.com'])->one();
            if (is_null($user) === false) {
                $user->delete();
            }
            $user = \common\models\User::find()->where(['email' => 'simonas@auto-loads.com'])->one();
            if (is_null($user) === false) {
                $user->delete();
            }
        } else {
            echo 'This migration can only run in DEBUG mode. [YII_DEBUG]\n';
        }
    }
}
