<?php

use yii\db\Migration;

/**
 * Class m180625_125226_update_service_table_fill_label_columns
 */
class m180625_125226_update_service_table_fill_label_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        \common\models\Service::updateAll(['label' => 'MEMBER1'], ['name' => 'MEMBER1']);
        \common\models\Service::updateAll(['label' => 'MEMBER12'], ['name' => 'MEMBER12']);
        \common\models\Service::updateAll(['label' => 'CREDITS200'], ['name' => 'CREDITS200']);
        \common\models\Service::updateAll(['label' => 'TRIAL'], ['name' => 'TRIAL']);
        \common\models\Service::updateAll(['label' => 'MEMBER024'], ['name' => 'MEMBER024']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        \common\models\Service::updateAll(['label' => null], ['name' => 'MEMBER1']);
        \common\models\Service::updateAll(['label' => null], ['name' => 'MEMBER12']);
        \common\models\Service::updateAll(['label' => null], ['name' => 'CREDITS200']);
        \common\models\Service::updateAll(['label' => null], ['name' => 'TRIAL']);
        \common\models\Service::updateAll(['label' => null], ['name' => 'MEMBER024']);
    }
}
