<?php

use yii\db\Schema;
use yii\db\Migration;

class m150126_133237_agency extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // Agency Apartments
        $this->createTable('{{%agency_apartments}}', [
            'apartment_id' => Schema::TYPE_PK . ' COMMENT "№"',
            'user_id' => 'int NOT NULL COMMENT "Пользователь"',
            'city_id' => 'int NOT NULL COMMENT "Город"',
            'closest_city_id' => 'int COMMENT "Ближайший город"',
            'address' => 'varchar(255) NOT NULL COMMENT "Адрес"',
            'apartment' => 'int COMMENT "Номер квартиры"',
            'district1' => 'int COMMENT "Округ"',
            'district2' => 'int COMMENT "Округ 2"',
            'floor' => 'tinyint NOT NULL DEFAULT 1 COMMENT "Этаж"',
            'total_rooms' => 'tinyint NOT NULL DEFAULT 1 COMMENT "Количество комнат"',
            'total_area' => 'int COMMENT "Общая площадь"',
            'beds' => 'smallint NOT NULL DEFAULT 1 COMMENT "Кол-во спальных мест"',
            'visible' => 'tinyint NOT NULL DEFAULT 1 COMMENT "Отображается на сайте"',
            'remont' => 'tinyint NOT NULL DEFAULT 1 COMMENT "Ремонт"',
            'metro_walk' => 'tinyint NOT NULL DEFAULT 0 COMMENT "Расстояние от метро"',
            'description' => 'text COMMENT "Описание"',
            'date_create' => 'datetime NULL DEFAULT NULL COMMENT "Дата создания"',
            'date_update' => 'datetime NULL DEFAULT NULL COMMENT "Дата редактирования"',
        ], $tableOptions . ' COMMENT = "Все апартаменты агенства"');

        // Index
        $this->createIndex('agency_apartments_user_id', '{{%agency_apartments}}', 'user_id');
        $this->createIndex('agency_apartments_city_id', '{{%agency_apartments}}', 'city_id');
        $this->createIndex('agency_apartments_closest_city_id', '{{%agency_apartments}}', 'closest_city_id');

        // Foreign Keys
        $this->addForeignKey('FK_agency_apartments_user_id', '{{%agency_apartments}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');


        // Apartment Images
        $this->createTable('{{%agency_apartment_images}}', [
            'image_id' => Schema::TYPE_PK . ' COMMENT "№"',
            'apartment_id' => 'int NOT NULL',
            'default_img' => 'tinyint NOT NULL DEFAULT 0 COMMENT "Заглавная картинка(1 да 0 нет)"',
            'preview' => 'varchar(255) COMMENT "Файл меленькой фотографии"',
            'review' => 'varchar(255) COMMENT "Файл большой фотографии"',
            'sort' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "Сортировка фото"',
        ], $tableOptions . ' COMMENT = "Изображения к апартаментам агенства"');

        // Index
        $this->createIndex('agency_apartment_images_apartment_id', '{{%agency_apartment_images}}', 'apartment_id');

        // Foreign Keys
        $this->addForeignKey('FK_agency_apartment_images_apartment_id', '{{%agency_apartment_images}}', 'apartment_id', '{{%agency_apartments}}', 'apartment_id', 'CASCADE', 'CASCADE');


        // Apartment Metro Stations
        $this->createTable('{{%agency_apartment_metro_stations}}', [
            'id' => Schema::TYPE_PK . ' COMMENT "№"',
            'apartment_id' => 'int NOT NULL',
            'metro_id' => 'int NOT NULL COMMENT "ID города"',
        ], $tableOptions . ' COMMENT = "Ближайшие станции метро к апартаментам агенства"');

        // Index        
        $this->createIndex('agency_apartment_metro_stations_apartment_id', '{{%agency_apartment_metro_stations}}', 'apartment_id');

        // Foreign Keys
        $this->addForeignKey('FK_agency_apartment_metro_stations_apartment_id', '{{%agency_apartment_metro_stations}}', 'apartment_id', '{{%agency_apartments}}', 'apartment_id', 'CASCADE', 'CASCADE');


        // Agency Adverts Связь между апартаментами и типами аренды(Политика аренды)
        $this->createTable('{{%agency_adverts}}', [
            'advert_id' => Schema::TYPE_PK . ' COMMENT "№"',
            'meta_title' => 'text NOT NULL COMMENT "Заголовок"',
            'meta_description' => 'text NOT NULL COMMENT "Описание"',
            'meta_keywords' => 'text NOT NULL COMMENT "Ключевые слова"',
            'apartment_id' => 'int NOT NULL COMMENT "Апартаменты"',
            'rent_type' => 'int NOT NULL COMMENT "Тип аренды"',
            'price' => 'decimal(12,5) DEFAULT "0.00000" COMMENT "Стоимость аренды"',
            'currency' => 'tinyint NOT NULL DEFAULT 1 COMMENT "Валюта"',
            'text' => 'varchar(255) COMMENT "Краткая информация"',
            'rules' => 'text COMMENT "Правила заселения"',
            'info' => 'text COMMENT "Дополнительная информация"',
            'position' => 'int COMMENT "Позиция"',
            'main_page' => 'tinyint NOT NULL DEFAULT 0 COMMENT "Объявление для главной"',
        ], $tableOptions . ' COMMENT = "Арендная политика апартаментов агенства"');

        // Index
        $this->createIndex('{{%agency_adverts_apartment_id_rent_type}}', '{{%agency_adverts}}', ['rent_type', 'apartment_id'], true);

        // Foreign Keys
        $this->addForeignKey('FK_agency_adverts_apartment_id', '{{%agency_adverts}}', 'apartment_id', '{{%agency_apartments}}', 'apartment_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK_agency_adverts_rent_type', '{{%agency_adverts}}', 'rent_type', '{{%realty_rent_type}}', 'rent_type_id', 'CASCADE', 'CASCADE');


        // Agency Apartment Reservations
        $this->createTable('{{%agency_apartment_reservations}}', [
            'reservation_id' => Schema::TYPE_PK . ' COMMENT "№"',
            'apartment_id' => 'int NOT NULL',
            'name' => 'varchar(255) COMMENT "Ф.И.О."',
            'email' => 'varchar(255) COMMENT "Е-мейл"',
            'clients_count' => 'tinyint NOT NULL DEFAULT 1 COMMENT "Количество клиентов"',
            'transfer' => 'tinyint NOT NULL DEFAULT 0 COMMENT "Трансфер"',
            'date_arrived' => 'datetime NULL DEFAULT NULL COMMENT "Время заезда"',
            'date_out' => 'datetime NULL DEFAULT NULL COMMENT "Время съезда"',
            'more_info' => 'varchar(255) COMMENT "Дополнительная информация"',
            'whau' => 'tinyint NOT NULL default 0 COMMENT "Откуда узнали о нас"',
            'phone' => 'bigint NOT NULL COMMENT "Телефон для связи"',
            'processed' => 'tinyint NOT NULL DEFAULT 0 COMMENT "Обработанная заявка"',
            'date_create' => 'datetime NULL DEFAULT NULL COMMENT "Дата создания заявки"',
        ], $tableOptions . ' COMMENT = "Заявки на бронирование Админа"');

        // Index
        $this->createIndex('agency_apartment_reservations_apartment_id', '{{%agency_apartment_reservations}}', 'apartment_id');

        // Foreign Keys
        $this->addForeignKey('FK_agency_apartment_reservations_apartment_id', '{{%agency_apartment_reservations}}', 'apartment_id', '{{%agency_apartments}}', 'apartment_id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%agency_apartment_reservations}}');
        $this->dropTable('{{%agency_adverts}}');
        $this->dropTable('{{%agency_apartment_metro_stations}}');
        $this->dropTable('{{%agency_apartment_images}}');
        $this->dropTable('{{%agency_apartments}}');
    }
}
