<?php

use yii\db\Schema;
use yii\db\Migration;

class m150126_133259_details extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // Agency details history
        $this->createTable('{{%agency_details_history}}', [
            'id' => Schema::TYPE_PK . ' COMMENT "№"',
            'phone' => 'bigint COMMENT "Телефон"',
            'email' => 'varchar(200) NOT NULL COMMENT "Почтовый адрес"',
            'type' => 'varchar(200) NOT NULL COMMENT "Тип реквизитов, платежная система, банк или телефон"',
            'advert_id' => 'int NOT NULL',
            'payment' => 'tinyint NOT NULL DEFAULT 1 COMMENT "Номер карты или расчетный счет"',
            'data' => 'text COMMENT "Отправленные данные в JSON формате"',
            'processed' => 'tinyint NOT NULL DEFAULT 0 COMMENT "Заявка обработана"',
            'date_create' => 'datetime NULL DEFAULT NULL COMMENT "Дата создания"',
        ], $tableOptions . ' COMMENT = "История рассылки реквизитов"');

        // Foreign Keys
        $this->addForeignKey('{{%agency_details_history_advert_id}}', '{{%agency_details_history}}', 'advert_id', '{{%agency_adverts}}', 'advert_id', 'CASCADE', 'CASCADE');
    }


    public function safeDown()
    {
        $this->dropTable('{{%agency_details_history}}');
    }
}
