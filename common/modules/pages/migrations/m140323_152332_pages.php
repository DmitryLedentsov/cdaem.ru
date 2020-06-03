<?php

use yii\db\Schema;

class m140323_152332_pages extends \yii\db\Migration
{
	public function safeUp()
	{
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

		// Static pages
		$this->createTable('{{%pages}}', [
            'page_id'       => Schema::TYPE_PK,
            'url'           => 'varchar(200) NOT NULL COMMENT "URL адрес страницы"',
            'title'         => 'text NOT NULL COMMENT "Заголовок"',
            'description'   => 'text NOT NULL COMMENT "Описание"',
            'keywords'      => 'text NOT NULL COMMENT "Ключевые слова"',
            'name'          => 'varchar(200) NOT NULL COMMENT "Название"',
            'text'          => 'mediumtext NOT NULL COMMENT "Текст страницы"',
			'status'        => 'tinyint(4) DEFAULT 0 COMMENT "Статус"',
			'active'        => 'tinyint(1) DEFAULT 1 COMMENT "Доступна страница или нет"',
            'UNIQUE(`url`)',
		], $tableOptions . ' COMMENT = "Статические страницы"');

		// Index
		$this->createIndex('pages_url', '{{%pages}}', 'url');
	}

	public function safeDown()
	{
		$this->dropTable('{{%pages}}');
	}
}
