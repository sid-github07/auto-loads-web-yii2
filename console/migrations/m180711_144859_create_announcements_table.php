<?php

use yii\db\Migration;

/**
 * Handles the creation of table `announcements`.
 */
class m180711_144859_create_announcements_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('announcements', [
            'id' => $this->primaryKey(),
            'topic' => $this->string()->null(),
            'body' => $this->text(),
            'status' => $this->tinyInteger()->defaultValue(\common\models\Announcement::STATUS_ACTIVE),
            'language_id' => $this->string()->null(),
            'expires_at' => $this->dateTime()->null(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()->null()
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('announcements');
    }
}
