<?php

use yii\db\Schema;
use yii\db\Migration;

class m150101_231642_partners extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // Partners Apartments
        $this->createTable('{{%partners_apartments}}', [
            'apartment_id' => Schema::TYPE_PK . ' COMMENT "№"',
            'user_id' => 'int NOT NULL COMMENT "Пользователь"',
            'city_id' => 'int NOT NULL COMMENT "Город"',
            'closest_city_id' => 'int COMMENT "Ближайший город"',
            'address' => 'varchar(255) NOT NULL COMMENT "Адрес"',
            'apartment' => 'int COMMENT "Номер квартиры"',
            'floor' => 'tinyint COMMENT "Этаж"',
            'total_rooms' => 'tinyint COMMENT "Количество комнат"',
            'total_area' => 'int COMMENT "Общая площадь"',
            'beds' => 'tinyint COMMENT "Кол-во спальных мест"',
            'visible' => 'tinyint NOT NULL DEFAULT 1 COMMENT "Отображается на сайте"',
            'status' => 'tinyint NOT NULL DEFAULT 0 COMMENT "Статус"',
            'remont' => 'tinyint COMMENT "Ремонт"',
            'metro_walk' => 'tinyint NOT NULL DEFAULT 0 COMMENT "Расстояние от метро"',
            'description' => 'text COMMENT "Описание"',
            'now_available' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 COMMENT "Сейчас свободно"',
            'open_contacts' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 COMMENT "Контакты открыты"',
            'date_create' => 'datetime DEFAULT NULL COMMENT "Дата создания"',
            'date_update' => 'datetime DEFAULT NULL COMMENT "Дата редактирования"',
        ], $tableOptions . ' COMMENT = "Апартаменты пользователей"');

        // Index
        $this->createIndex('{{%partners_apartments_user_id}}', '{{%partners_apartments}}', 'user_id');
        $this->createIndex('{{%partners_apartments_city_id}}', '{{%partners_apartments}}', 'city_id');
        $this->createIndex('{{%partners_apartments_closest_city_id}}', '{{%partners_apartments}}', 'closest_city_id');

        // Foreign Keys
        $this->addForeignKey('{{%partners_apartments_user_id}}', '{{%partners_apartments}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');


        // Partners Apartment Images
        $this->createTable('{{%partners_apartment_images}}', [
            'image_id' => Schema::TYPE_PK . ' COMMENT "№"',
            'apartment_id' => 'int NOT NULL',
            'default_img' => 'tinyint NOT NULL DEFAULT 0 COMMENT "Заглавная картинка"',
            'preview' => 'varchar(255) COMMENT "Файл меленькой фотографии"',
            'review' => 'varchar(255) COMMENT "Файл большой фотографии"',
            'sort' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "Сортировка фото"',
        ], $tableOptions . ' COMMENT = "Изображения к апартаментам"');

        // Index
        $this->createIndex('{{%partners_apartment_images_apartment_id}}', '{{%partners_apartment_images}}', 'apartment_id');

        // Foreign Keys
        $this->addForeignKey('{{%partners_apartment_images_apartment_id}}', '{{%partners_apartment_images}}', 'apartment_id', '{{%partners_apartments}}', 'apartment_id', 'CASCADE', 'CASCADE');


        // Partners Apartment Adverts Связь между апартаментами и типами аренды(Политика аренды)
        $this->createTable('{{%partners_adverts}}', [
            'advert_id' => Schema::TYPE_PK . ' COMMENT "№"',
            'apartment_id' => 'int NOT NULL COMMENT "Апартаменты"',
            'rent_type' => 'int NOT NULL COMMENT "Тип аренды"',
            'price' => 'decimal(12,5) DEFAULT "0.00000" COMMENT "Стоимость аренды"',
            'currency' => 'tinyint NOT NULL DEFAULT 1 COMMENT "Валюта"',
            'position' => 'int NOT NULL DEFAULT 0 COMMENT "Позиция"',
            'old_position' => 'int NOT NULL DEFAULT 0 COMMENT "Прошлая позиция"',
            'real_position' => 'int NULL DEFAULT NULL COMMENT "Резервная позиция, позволяет исправить ошибку с модерацией и отображением. Не null только в том случае если нужно к ней вернуться."',
            'top' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 COMMENT "Топ объявление"',
            'selected' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 COMMENT "Выделенное объявление"',
        ], $tableOptions . ' COMMENT = "Объявления о сдачи в аренду апартаментов"');

        $this->createIndex('{{%partners_adverts_apartment_id_rent_type}}', '{{%partners_adverts}}', ['rent_type', 'apartment_id'], true);

        // Foreign Keys
        $this->addForeignKey('{{%partners_adverts_apartment_id}}', '{{%partners_adverts}}', 'apartment_id', '{{%partners_apartments}}', 'apartment_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('{{%partners_adverts_rent_type}}', '{{%partners_adverts}}', 'rent_type', '{{%realty_rent_type}}', 'rent_type_id', 'CASCADE', 'CASCADE');


        // Apartment Metro Stations
        $this->createTable('{{%partners_apartment_metro_stations}}', [
            'id' => Schema::TYPE_PK . ' COMMENT "№"',
            'apartment_id' => 'int NOT NULL COMMENT "ID города"',
            'metro_id' => 'int NOT NULL COMMENT "ID города"',
        ], $tableOptions . ' COMMENT = "Ближайшие станции метро к апартаментам"');

        // Index
        $this->createIndex('{{%partners_apartment_metro_stations_apartment_id}}', '{{%partners_apartment_metro_stations}}', 'apartment_id');

        // Foreign Keys
        $this->addForeignKey('{{%partners_apartment_metro_stations_apartment_id}}', '{{%partners_apartment_metro_stations}}', 'apartment_id', '{{%partners_apartments}}', 'apartment_id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%partners_apartment_metro_stations}}');
        $this->dropTable('{{%partners_apartment_images}}');
        $this->dropTable('{{%partners_adverts}}');
        $this->dropTable('{{%partners_apartments}}');
    }
}
