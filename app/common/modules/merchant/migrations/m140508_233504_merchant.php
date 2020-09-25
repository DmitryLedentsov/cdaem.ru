<?php

use yii\db\Schema;

class m140508_233504_merchant extends \yii\db\Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // Денежный оборот
        $this->createTable('{{%merchant_payments}}', [
            'payment_id' => Schema::TYPE_PK,
            'module' => 'varchar(255) NOT NULL  COMMENT "Модуль, который произвел операцию"',
            'type' => 'varchar(255) NOT NULL  COMMENT "Тип (например начисления)"',
            'system' => 'varchar(255) NOT NULL  COMMENT "Система (например выплата)"',
            'user_id' => 'int(11)',
            'date' => 'datetime DEFAULT NULL',
            'funds' => Schema::TYPE_MONEY . ' NOT NULL DEFAULT "0" COMMENT "Средства"',
            'funds_was' => Schema::TYPE_MONEY . ' NOT NULL DEFAULT "0" COMMENT "Средства, которые были на счету до сделки"',
            'data' => 'text COMMENT "Дополнительные данные (например идентификатор перевода средств)"',
        ], $tableOptions . ' COMMENT = "История денежного оборота"');

        // Index
        $this->createIndex('merchant_payments_user_id', '{{%merchant_payments}}', 'user_id');

        // Foreign Keys
        $this->addForeignKey('FK_merchant_payments_user_id', '{{%merchant_payments}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');


        // Добавляем счет пользователя
        $this->addColumn('{{%users}}', 'funds_main', Schema::TYPE_MONEY . ' NOT NULL DEFAULT 0');
    }

    public function safeDown()
    {
        $this->execute('ALTER TABLE {{%users}} DROP `funds_main`');
        $this->dropTable('{{%merchant_payments}}');
    }
}
