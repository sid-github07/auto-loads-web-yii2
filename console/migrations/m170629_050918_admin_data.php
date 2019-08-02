<?php

use common\models\Admin;
use yii\db\Migration;

/**
 * Class m170629_050918_admin_data
 */
class m170629_050918_admin_data extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->insert(Admin::tableName(), [
            'id' => 1,
            'name' => 'Simonas',
            'surname' => 'Niedvaras',
            'email' => 'simonas@auto-loads.com',
            'phone' => '+37067022971',
            'auth_key' => 'bT6Zi691PKWc5ZqsRFjev10pE3B3iYsp',
            'password_hash' => '$2y$13$GgvOhgWcb9gy7pfxUNCS8.Onq6D/Zk7xjiWPu5AFqosLxzMILHIJO',
            'password_reset_token' => Admin::DEFAULT_PASSWORD_RESET_TOKEN,
            'admin' => Admin::IS_ADMIN,
            'archived' => Admin::NOT_ARCHIVED,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert(Admin::tableName(), [
            'id' => 2,
            'name' => 'Artūras',
            'surname' => 'Černauskas',
            'email' => 'arturas@itpc.lt',
            'phone' => '+37062459894',
            'auth_key' => 'NFe-qlhZpl6feXL6L09zPZtaRzrQH2tE',
            'password_hash' => '$2y$13$AQUydiWPHJgleCZ1mCO80.dbJUTEkOJsx.vMA/4XyIBJEXrg0.HNK',
            'password_reset_token' => Admin::DEFAULT_PASSWORD_RESET_TOKEN,
            'admin' => Admin::IS_ADMIN,
            'archived' => Admin::NOT_ARCHIVED,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert(Admin::tableName(), [
            'id' => 7418,
            'name' => 'Laimutė',
            'surname' => 'Šauklienė',
            'email' => 'laimutesaukliene24@gmail.com',
            'phone' => '+37061004016',
            'auth_key' => 'Fx3GuiVCZVredHm_TBxQODVGVRg1PhE6',
            'password_hash' => '$2y$13$wCyg5y2vzYVBHimCqWRbEOCHH5oPn4loEHLc/1VE7Z4kTMuyAylm6',
            'password_reset_token' => Admin::DEFAULT_PASSWORD_RESET_TOKEN,
            'admin' => Admin::IS_MODERATOR,
            'archived' => Admin::NOT_ARCHIVED,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        
        $this->insert(Admin::tableName(), [
            'id' => 3,
            'name' => 'Jonas',
            'surname' => 'Slivka',
            'email' => 'jonuco@gmail.com',
            'phone' => '+37067819938',
            'auth_key' => 'qyDTJ7048vA1-Hke5A-en09qCt78rUXS',
            'password_hash' => '$2y$13$.VwL1.plB24rNjCTSCFvyekPGAn0wyujSELoX6K3BVo2QrNWUsiUe',
            'password_reset_token' => Admin::DEFAULT_PASSWORD_RESET_TOKEN,
            'admin' => Admin::IS_ADMIN,
            'archived' => Admin::NOT_ARCHIVED,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert(Admin::tableName(), [
            'id' => 4,
            'name' => 'Mindaugas',
            'surname' => 'Kasparavičius',
            'email' => 'kasp.mindaugas@gmail.com',
            'phone' => '+37067819938',
            'auth_key' => 'H68_MpuzvqC5nScuEAgYpGnY0OEftP80',
            'password_hash' => '$2y$13$kcm4PMXsZAKGFyNPKNbl2.u26SU4cxKgrBiisThukx5Loa8M16e1.',
            'password_reset_token' => Admin::DEFAULT_PASSWORD_RESET_TOKEN,
            'admin' => Admin::IS_ADMIN,
            'archived' => Admin::NOT_ARCHIVED,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->truncateTable(Admin::tableName());
    }
}
