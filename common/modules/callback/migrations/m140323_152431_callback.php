<?php

use yii\db\Schema;

class m140323_152431_callback extends \yii\db\Migration
{
	public function safeUp()
	{
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

		// Сallback
		$this->createTable('{{%callback}}', [
            'callback_id'    => Schema::TYPE_PK,
            'phone'          => 'bigint COMMENT "Телефон"',
            'active'         => 'tinyint(1) DEFAULT 0 COMMENT "Оператор перезвонил или нет"',
            'date_create'    => 'datetime DEFAULT NULL COMMENT "Дата создания"',
            'date_processed' => 'datetime DEFAULT NULL COMMENT "Дата обработки"'
		], $tableOptions . ' COMMENT = "Заявки на обратный звонок"');
	}

	public function safeDown()
	{
		$this->dropTable('{{%callback}}');
	}
}
