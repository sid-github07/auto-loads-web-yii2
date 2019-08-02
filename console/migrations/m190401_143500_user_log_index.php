<?php

use common\models\UserLog as Model;

use yii\db\Migration;

class m190401_143500_user_log_index extends Migration
{
    protected $tableName;
    protected $idxNamePrefix = '';

    public function init()
    {
        parent::init();

        $this->tableName = Model::tableName();
        $this->idxNamePrefix = 'idx-' . preg_replace('/{{%(.*?)}}/', '\1', Model::tableName());
    }

    public function safeUp()
    {
        $this->createIndex("{$this->idxNamePrefix}-action",     $this->tableName, 'action');
        $this->createIndex("{$this->idxNamePrefix}-created_at", $this->tableName, 'created_at');
        $this->createIndex("{$this->idxNamePrefix}-updated_at", $this->tableName, 'updated_at');
    }

    public function safeDown()
    {
        $this->dropIndex("{$this->idxNamePrefix}-updated_at", $this->tableName);
        $this->dropIndex("{$this->idxNamePrefix}-created_at", $this->tableName);
        $this->dropIndex("{$this->idxNamePrefix}-action",     $this->tableName);
    }
}
