<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * Class UserCreditService
 * @package common\models
 */
class UserCreditService extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_credit_services}}';
    }

    /**
     * Announcement constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        if ($this->isNewRecord) {
            $this->created_at = date('Y-m-d H:i:s');
        } else {
            $this->updated_at = date('Y-m-d H:i:s');
        }
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['user_id', 'credit_service_id', 'entity_id', 'credit_service_type', 'entity_type'], 'required'],
            [['user_id'], 'integer',],
            [['credit_service_id'], 'integer',],
            [['entity_id'], 'integer',],
            [['entity_type'], 'string',],
            [['credit_service_type'], 'integer',],
        ];
    }
}
