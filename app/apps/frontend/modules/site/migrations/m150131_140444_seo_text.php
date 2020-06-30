<?php

use yii\db\Schema;

class m150131_140444_seo_text extends \yii\db\Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // Static pages
        $this->createTable('{{%seo_text}}', [
            'text_id' => 'int(11) unsigned NOT NULL AUTO_INCREMENT',
            'url' => 'varchar(200) NOT NULL COMMENT "URL адрес страницы"',
            'type' => 'varchar(200) NOT NULL COMMENT "Тип текста"',
            'text' => 'text  NOT NULL COMMENT "Текст"',
            'active' => 'tinyint(1) DEFAULT 1 COMMENT "Доступен текст или нет"',
            'unique(`type`, `url`)',
            'PRIMARY KEY (`text_id`,`url`)',
        ], $tableOptions . ' COMMENT = "Seo текст"');

        // Index
        $this->createIndex('seo_text_url', '{{%seo_text}}', 'url');
    }

    public function safeDown()
    {
        $this->dropTable('{{%seo_text}}');
    }
}
