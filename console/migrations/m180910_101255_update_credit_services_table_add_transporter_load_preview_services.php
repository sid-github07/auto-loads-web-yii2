<?php

use yii\db\Migration;
use common\models\CreditService;
use yii\db\Query;

/**
 * Class m180910_101255_update_credit_services_table_add_transporter_load_preview_services
 */
class m180910_101255_update_credit_services_table_add_transporter_load_preview_services extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert(CreditService::tableName(), [
            'credit_type' => CreditService::CREDIT_TYPE_CAR_TRANSPORTER_PREVIEW_VIEW,
            'credit_cost' => 2,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $this->insert(CreditService::tableName(), [
            'credit_type' => CreditService::CREDIT_TYPE_LOAD_PREVIEW_VIEW,
            'credit_cost' => 2,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new Query)
            ->createCommand()
            ->delete(CreditService::tableName(), ['credit_type' => CreditService::CREDIT_TYPE_CAR_TRANSPORTER_PREVIEW_VIEW])
            ->execute();

        (new Query)
            ->createCommand()
            ->delete(CreditService::tableName(), ['credit_type' => CreditService::CREDIT_TYPE_LOAD_PREVIEW_VIEW])
            ->execute();

    }
}
