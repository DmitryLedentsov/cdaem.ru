<?php

use yii\db\Schema;
use yii\db\Migration;

class m140928_181957_helpdesk extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // Tickets
        $this->createTable(
            '{{%helpdesk}}',
            [
            'ticket_id' => Schema::TYPE_PK,
            'user_id' => 'int(11) DEFAULT NULL',
            'email' => 'varchar(255) DEFAULT NULL',
            'theme' => 'varchar(100) NOT NULL COMMENT "Тема обращения"',
            'user_name' => 'varchar(100) NOT NULL COMMENT "Имя неавторизированного пользователя"',
            'text' => 'text COMMENT "Обращение"',
            'date_create' => 'datetime DEFAULT NULL COMMENT "Дата создания"',
            'date_close' => 'datetime DEFAULT NULL COMMENT "Дата закрытия"',
            'priority' => 'tinyint(4) NOT NULL DEFAULT "0" COMMENT "Приоритет"',
            'answered' => 'tinyint(1) NOT NULL DEFAULT "0" COMMENT "Есть или нет ответа"',
            'close' => 'tinyint(1) NOT NULL DEFAULT "0" COMMENT "Открытый или закрытый тикет"',
            'department' => 'varchar(255) NOT NULL  COMMENT "Отдел тех. поддержки"',
            'ip' => 'varchar(100)',
            'user_agent' => 'varchar(255)',
        ],
            $tableOptions . ' COMMENT = "Тикеты в тех. поддержку"'
        );


        // Tickets answers
        $this->createTable(
            '{{%helpdesk_answers}}',
            [
            'answer_id' => Schema::TYPE_PK,
            'ticket_id' => 'int(11) NOT NULL',
            'user_id' => 'int(11) NOT NULL',
            'text' => 'text',
            'date' => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
        ],
            $tableOptions . ' COMMENT = "Ответы тех. поддержки"'
        );

        // Index
        $this->createIndex('helpdesk_answers_ticket_id', '{{%helpdesk_answers}}', 'ticket_id');
        $this->createIndex('helpdesk_answers_user_id', '{{%helpdesk_answers}}', 'user_id');

        // Foreign Keys
        $this->addForeignKey('{{%helpdesk_user_id}}', '{{%helpdesk}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('{{%helpdesk_answers_ticket_id}}', '{{%helpdesk_answers}}', 'ticket_id', '{{%helpdesk}}', 'ticket_id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%helpdesk_answers}}');
        $this->dropTable('{{%helpdesk}}');
    }
}
