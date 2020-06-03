<?php

use nepster\users\helpers\Security;
use yii\db\Migration;
use yii\db\Schema;

/**
 * Социальный лист пользователей
 */
class m140418_204071_users_list extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // Пользователи
        $this->createTable('{{%users_list}}', [
            'record_id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'interlocutor_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'type' => 'tinyint NOT NULL DEFAULT 1 COMMENT "Тип (Заметка или Бан)"',
        ], $tableOptions . ' COMMENT = "Социальный лист пользователей"');

        // Индексы
        $this->createIndex('{{%users_list_user_id}}', '{{%users_list}}', 'user_id');
        $this->createIndex('{{%users_list_interlocutor_id}}', '{{%users_list}}', 'interlocutor_id');

        // Ключи
        $this->addForeignKey('{{%users_list_user_id}}', '{{%users_list}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('{{%users_list_interlocutor_id}}', '{{%users_list}}', 'interlocutor_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%users_list}}');
    }
}
