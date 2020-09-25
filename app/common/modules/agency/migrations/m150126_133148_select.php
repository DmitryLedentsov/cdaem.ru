<?php

use yii\db\Schema;
use yii\db\Migration;

class m150126_133148_select extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // Agency Apartment Select
        $this->createTable('{{%agency_apartment_select}}', [
            'apartment_select_id' => Schema::TYPE_PK . ' COMMENT "№"',
            'name' => 'varchar(255) NOT NULL COMMENT "Ф.И.О."',
            'phone' => 'bigint COMMENT "Телефон"',
            'phone2' => 'bigint COMMENT "Второй телефон"',
            'email' => 'varchar(200) NOT NULL COMMENT "Почтовый адрес"',
            'rent_types' => 'text COMMENT "Типы аренды в JSON формате"',
            'rooms' => 'tinyint NOT NULL DEFAULT 1 COMMENT "Кол-во комнат"',
            'description' => 'text COMMENT "Дополнительная информация"',
            'metro' => 'text COMMENT "Станции метро в JSON формате"',
            'status' => 'tinyint NOT NULL DEFAULT 0 COMMENT "Статус заявки"',
        ], $tableOptions . ' COMMENT = "Заявки на подбор апартаментов"');
    }

    public function safeDown()
    {
        $this->dropTable('{{%agency_apartment_select}}');
    }
}
