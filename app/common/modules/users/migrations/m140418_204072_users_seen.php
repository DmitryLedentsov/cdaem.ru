<?php

use yii\db\Schema;
use yii\db\Migration;
use nepster\users\helpers\Security;

/**
 * Просмотры пользователей
 */
class m140418_204072_users_seen extends Migration
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
        $this->createTable('{{%user_seen}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'table_name' => $this->string()->notNull(),
            'type' => $this->string(),
            'last_update' => $this->dateTime(),
        ], $tableOptions . ' COMMENT = "Социальный лист пользователей"');

        $this->addForeignKey('{{%user_seen_user_id}}', '{{%user_seen}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_seen}}');
    }
}
