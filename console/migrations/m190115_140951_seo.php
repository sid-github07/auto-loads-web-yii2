<?php

use yii\db\Migration;

class m190115_140951_seo extends Migration
{

    public function init()
    {
        $this->db = 'db';
        parent::init();
    }

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB'; // utf8mb4 to use correct 4 bytes max instead of 3 bytes

        $this->createTable(
            '{{%seo}}',
            [
                'page' => $this->string(50)->notNull(),
                'route' => $this->string(50)->notNull(),
                'domain' => $this->string(10)->notNull(),
                'title' => $this->string(100)->notNull(),
                'keywords' => $this->text()->notNull(),
                'description' => $this->text()->notNull(),
            ], $tableOptions
        );
        $this->createIndex('by_page_and_domain', '{{%seo}}', ['page', 'domain'], true);
        $this->createIndex('by_route_and_domain', '{{%seo}}', ['route', 'domain'], true);

    }

    public function safeDown()
    {
        $this->dropIndex('by_page_and_domain', '{{%seo}}');
        $this->dropIndex('by_route_and_domain', '{{%seo}}');
        $this->dropTable('{{%seo}}');
    }
}
