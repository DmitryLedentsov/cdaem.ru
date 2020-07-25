<?php

use yii\db\Schema;
use yii\db\Migration;

class m150101_232638_messages extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        /**
         * Сообщения
         */
        $this->createTable('{{%user_messages}}', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID сообщения"',
            'theme' => Schema::TYPE_STRING . ' COMMENT "Тема"',
            'text' => Schema::TYPE_TEXT . ' COMMENT "Текст"',
            'date_create' => Schema::TYPE_DATETIME . ' COMMENT "Дата создания"',
        ], $tableOptions);


        /**
         * Почтовый ящик сообщений
         */
        $this->createTable('{{%user_messages_mailbox}}', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID сообщения"',
            'message_id' => Schema::TYPE_INTEGER . ' COMMENT "ID base сообщения"',
            'user_id' => Schema::TYPE_INTEGER . ' COMMENT "ID пользователя"',
            'interlocutor_id' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "Собеседник"',
            'read' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 COMMENT "Прочитано"',
            'inbox' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 COMMENT "Входящее/Исходящее"',
            'bin' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 COMMENT "В корзине"',
            'deleted' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 COMMENT "Удалено"',
        ], $tableOptions);

        $this->createIndex('{{%user_messages_mailbox_user_id_interlocutor_id}}', '{{%user_messages_mailbox}}', ['user_id', 'interlocutor_id']);

        $this->addForeignKey('{{%user_messages_mailbox_message_id}}', '{{%user_messages_mailbox}}', 'message_id', '{{%user_messages}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('{{%user_messages_mailbox_user_id}}', '{{%user_messages_mailbox}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('{{%user_messages_mailbox_interlocutor_id}}', '{{%user_messages_mailbox}}', 'interlocutor_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%user_messages_mailbox}}');
        $this->dropTable('{{%user_messages}}');
    }
}
