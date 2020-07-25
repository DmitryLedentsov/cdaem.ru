<?php

use yii\db\Schema;
use yii\db\Migration;

class m150101_231655_partners_adv extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // Таблица для рекламы в слайдере
        $this->createTable('{{%partners_advertisement_slider}}', [
            'advertisement_id' => Schema::TYPE_PK,
            'type' => 'tinyint NOT NULL DEFAULT 1 COMMENT "Тип рекламы"',
            'label' => 'tinyint NOT NULL DEFAULT 1 COMMENT "Текст в блоке рекламы"',
            'advert_id' => 'int DEFAULT NULL COMMENT "ID объявления"',
            'user_id' => 'int NOT NULL COMMENT "Пользователь"',
            'more_info' => 'varchar(255) DEFAULT NULL COMMENT "Дополнительная информация"',
            'date_create' => 'datetime DEFAULT NULL COMMENT "Дата создания"',
            'date_payment' => 'datetime DEFAULT NULL COMMENT "Дата оплаты"',
            'visible' => 'tinyint NOT NULL DEFAULT 1 COMMENT "Показывать"',
            'payment' => 'tinyint NOT NULL DEFAULT 0 COMMENT "Оплата"',
        ], $tableOptions . ' COMMENT = "Реклама доски объявлений в слайдере"');

        // Foreign Keys
        $this->addForeignKey('{{%partners_advertisement_slider_advert_id}}', '{{%partners_advertisement_slider}}', 'advert_id', '{{%partners_adverts}}', 'advert_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('{{%partners_advertisement_slider_user_id}}', '{{%partners_advertisement_slider}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');


        // Таблица рекламирования партнерских апартаментов
        $this->createTable('{{%partners_advertisement}}', [
            'advertisement_id' => Schema::TYPE_PK,
            'advert_id' => 'int NOT NULL COMMENT "ID объявления"',
            'text' => 'varchar(255) COMMENT "Текст рекламы"',
        ], $tableOptions . ' COMMENT = "Реклама доски объявлений"');

        // Foreign Keys
        $this->addForeignKey('{{%partners_advertisement_advert_id}}', '{{%partners_advertisement}}', 'advert_id', '{{%partners_adverts}}', 'advert_id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%partners_advertisement_slider}}');
        $this->dropTable('{{%partners_advertisement}}');
    }
}
