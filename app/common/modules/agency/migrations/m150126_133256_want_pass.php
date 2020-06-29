<?php

use yii\db\Schema;
use yii\db\Migration;

class m150126_133256_want_pass extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // Agency Apartment Want Pass
        $this->createTable('{{%agency_apartment_want_pass}}', [
            'apartment_want_pass_id' => Schema::TYPE_PK . ' COMMENT "№"',
            'name'  => 'varchar(255) NOT NULL COMMENT "Ф.И.О."',
            'phone' => 'bigint COMMENT "Телефон"',
            'phone2' => 'bigint COMMENT "Второй телефон"',
            'email' => 'varchar(200) NOT NULL COMMENT "Почтовый адрес"',
            'rent_types' => 'text COMMENT "Типы аренды в JSON формате"',
            'rooms' => 'tinyint NOT NULL DEFAULT 1 COMMENT "Кол-во комнат"',
            'description' => 'text COMMENT "Дополнительная информация"',
            'address' => 'varchar(255) NOT NULL COMMENT "Адрес"',
            'metro' => 'text COMMENT "Станции метро в JSON формате"',
            'images' => 'text COMMENT "Изображения в JSON формате"',
            'status' => 'tinyint NOT NULL DEFAULT 0 COMMENT "Статус заявки"',
            'date_create' => 'datetime DEFAULT NULL COMMENT "Дата создания"',
        ], $tableOptions . ' COMMENT = "Заявки на Хочу сдать квартиру"');
    }

    public function safeDown()
    {
        $this->dropTable('{{%agency_apartment_want_pass}}');
    }
}
