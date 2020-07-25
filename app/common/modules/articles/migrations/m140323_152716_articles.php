<?php

use yii\db\Schema;

class m140323_152716_articles extends \yii\db\Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // Article
        $this->createTable('{{%articles}}', [
            'article_id' => Schema::TYPE_PK,
            'slug' => 'varchar(255) NOT NULL COMMENT "Название статьи на транслите"',
            'title_img' => 'varchar(255) NOT NULL COMMENT "Заглавная картинка"',
            'name' => 'varchar(255) NOT NULL COMMENT "Название"',
            'short_text' => 'varchar(255) NOT NULL COMMENT "Краткое описание"',
            'title' => 'varchar(255) NOT NULL COMMENT "Заголовок"',
            'description' => 'varchar(255) NOT NULL COMMENT "Описание"',
            'keywords' => 'varchar(255) NOT NULL COMMENT "Ключевые слова"',
            'full_text' => 'text  NOT NULL COMMENT "Текст"',
            'visible' => 'tinyint(4) DEFAULT 1 COMMENT "Видимость"',
            'status' => 'tinyint(4) DEFAULT 0 COMMENT "Статус"',
            'date_create' => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00" COMMENT "Дата создания"',
        ], $tableOptions . ' COMMENT = "Статические страницы"');

        // Index
        $this->createIndex('article_slug', '{{%articles}}', 'slug');
    }

    public function safeDown()
    {
        $this->dropTable('{{%articles}}');
    }
}
