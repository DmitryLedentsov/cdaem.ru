<?php

use yii\db\Schema;
use yii\db\Migration;

class m150101_231660_partners_reservations extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // Apartment Reservations
        $this->createTable('{{%partners_reservations}}', [
            'id' => Schema::TYPE_PK . ' COMMENT "№"',
            'user_id' => 'int NOT NULL COMMENT "ID пользователя"',
            'rent_type' => 'int DEFAULT NULL COMMENT "Тип аренды"',
            'city_id' => 'int DEFAULT NULL COMMENT "Город"',
            'address' => 'varchar(255) DEFAULT NULL COMMENT "Адрес"',
            'children' => 'tinyint NOT NULL DEFAULT 0 COMMENT "С детьми"',
            'pets' => 'tinyint NOT NULL DEFAULT 0 COMMENT "С животными"',
            'clients_count' => 'tinyint NOT NULL DEFAULT 1 COMMENT "Количество клиентов"',
            'more_info' => 'varchar(255) COMMENT "Дополнительная информация"',
            'money_from' => 'int NOT NULL DEFAULT 0 COMMENT "Планируемый бюджет от"',
            'money_to' => 'int NOT NULL DEFAULT 0 COMMENT "Планируемый бюджет до"',
            'currency' => 'tinyint NOT NULL DEFAULT 1 COMMENT "Валюта"',
            'rooms' => 'tinyint DEFAULT NULL COMMENT "Кол-во комнат"',
            'beds' => 'tinyint DEFAULT NULL COMMENT "Кол-во спальных мест"',
            'floor' => 'tinyint DEFAULT NULL COMMENT "Этаж"',
            'metro_walk' => 'tinyint DEFAULT NULL COMMENT "Пешком к метро"',
            'date_arrived' => 'datetime DEFAULT NULL COMMENT "Время заезда"',
            'date_out' => 'datetime DEFAULT NULL COMMENT "Время съезда"',
            'closed' => 'tinyint NOT NULL DEFAULT 0 COMMENT "Закрыта"',
            'cancel' => 'tinyint NOT NULL DEFAULT 0 COMMENT "Отменена"',
            'cancel_reason' => 'varchar(255) COMMENT "Причина отмены"',
            'date_create' => 'datetime DEFAULT NULL COMMENT "Дата создания заявки"',
            'date_update' => 'datetime DEFAULT NULL COMMENT "Дата редактирования"',
            'date_actuality' => 'datetime DEFAULT NULL COMMENT "Дата актуальности заявки"',
        ], $tableOptions . ' COMMENT = "Заявки на бронирование"');


        // Foreign Keys
        $this->addForeignKey('{{%partners_reservations_user_id}}', '{{%partners_reservations}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('{{%partners_reservations_rent_type}}', '{{%partners_reservations}}', 'rent_type', '{{%realty_rent_type}}', 'rent_type_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('{{%partners_reservations_city_id}}', '{{%partners_reservations}}', 'city_id', '{{%city}}', 'city_id', 'CASCADE', 'CASCADE');


        // Открытие контактов Заявок на бронирование
        $this->createTable('{{%partners_reservations_payment}}', [
            'id' => Schema::TYPE_PK . ' COMMENT "№"',
            'reservation_id' => 'int NOT NULL COMMENT "№ заявки"',
            'user_id' => 'int NOT NULL COMMENT "ID пользователя"',
            'date_create' => 'datetime DEFAULT NULL COMMENT "Дата оплаты"',
        ], $tableOptions . ' COMMENT = "Оплата открытия контактов"');


        // Foreign Keys
        $this->addForeignKey('{{%partners_reservations_payment_reservation_id}}', '{{%partners_reservations_payment}}', 'reservation_id', '{{%partners_reservations}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('{{%partners_reservations_payment_user_id}}', '{{%partners_reservations_payment}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');


        // Брони к адвертам
        $this->createTable('{{%partners_advert_reservations}}', [
            'id' => Schema::TYPE_PK . ' COMMENT "№"',
            'user_id' => 'int NOT NULL COMMENT "ID арендатора"',
            'landlord_id' => 'int NOT NULL COMMENT "ID арендодателя"',
            'advert_id' => 'int NOT NULL COMMENT "ID объявления"',
            'children' => 'tinyint NOT NULL DEFAULT 0 COMMENT "Наличие детей"',
            'pets' => 'tinyint NOT NULL DEFAULT 0 COMMENT "Наличие домашних животных"',
            'clients_count' => 'tinyint NOT NULL DEFAULT 1 COMMENT "Кол-во клиентов"',
            'more_info' => 'varchar(255) COMMENT "Дополнительная информация"',
            'date_arrived' => 'datetime DEFAULT NULL COMMENT "Дата заезда"',
            'date_out' => 'datetime DEFAULT NULL COMMENT "Дата выезда"',
            'date_actuality' => 'datetime DEFAULT NULL COMMENT "Актуально до"',
            'date_create' => 'datetime DEFAULT NULL COMMENT "Дата создания"',
            'date_update' => 'datetime DEFAULT NULL COMMENT "Дата редактирования"',
            'landlord_open_contacts' => 'tinyint NOT NULL DEFAULT 0 COMMENT "Арендодатель открыл контакты"',
            'cancel' => 'tinyint NOT NULL DEFAULT 0 COMMENT "Отменена"',
            'cancel_reason' => 'varchar(255) COMMENT "Причина отмены"',
            'confirm' => 'tinyint NOT NULL DEFAULT 0 COMMENT "Подтверждена"',
            'closed' => 'tinyint NOT NULL DEFAULT 0 COMMENT "Закрыта"',
        ], $tableOptions . ' COMMENT = "Резервации объявлений"');

        // Foreign Keys
        $this->addForeignKey('{{%partners_advert_reservations_advert_id}}', '{{%partners_advert_reservations}}', 'advert_id', '{{%partners_adverts}}', 'advert_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('{{%partners_advert_reservations_user_id}}', '{{%partners_advert_reservations}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('{{%partners_advert_reservations_landlord_id}}', '{{%partners_advert_reservations}}', 'landlord_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');


        // Сделки по резервациям к объявлениям
        $this->createTable('{{%partners_advert_reservations_deal}}', [
            'id' => Schema::TYPE_PK . ' COMMENT "№"',
            'reservation_id' => Schema::TYPE_INTEGER . ' COMMENT "№ брони"',
            'payment_owner' => 'tinyint NOT NULL DEFAULT 0 COMMENT "Заявка оплачена владельцем"',
            'payment_client' => 'tinyint NOT NULL DEFAULT 0 COMMENT "Заявка оплачена клиентом"',
            'date_payment_owner' => 'datetime DEFAULT NULL COMMENT "Дата оплаты владельца"',
            'date_payment_client' => 'datetime DEFAULT NULL COMMENT "Дата оплаты клиента"',
            'funds_owner' => Schema::TYPE_MONEY . ' NOT NULL DEFAULT "0" COMMENT "Средства оплаченые владельцем"',
            'funds_client' => Schema::TYPE_MONEY . ' NOT NULL DEFAULT "0" COMMENT "Средства оплаченые клиентом"',
        ], $tableOptions . ' COMMENT = "Организация сделок бронирования между клиентом и владельцем"');


        // Foreign Keys
        $this->addForeignKey('{{%partners_advert_reservations_deal_reservation_id}}', '{{%partners_advert_reservations_deal}}', 'reservation_id', '{{%partners_advert_reservations}}', 'id', 'CASCADE', 'CASCADE');


        // Заявки "Незаезд"
        $this->createTable('{{%partners_advert_reservations_failure}}', [
            'id' => Schema::TYPE_PK . ' COMMENT "№"',
            'reservation_id' => Schema::TYPE_INTEGER . ' COMMENT "№ Брони"',
            'user_id' => 'int NOT NULL COMMENT "ID пользователя"',
            'cause_text' => 'text COMMENT "Причина"',
            'date_create' => 'datetime DEFAULT NULL COMMENT "Дата создания"',
            'date_update' => 'datetime DEFAULT NULL COMMENT "Дата редактирования"',
            'date_to_process' => 'datetime DEFAULT NULL COMMENT "Дата для возврата"',
            'processed' => 'tinyint NOT NULL DEFAULT 0 COMMENT  "Обработана системой"',
            'closed' => 'tinyint NOT NULL DEFAULT 0 COMMENT "Обработана администратором"'
        ], $tableOptions . ' COMMENT = "Заявки \"Незаезд\""');

        // Foreign Keys
        $this->addForeignKey('{{%partners_advert_reservations_failure_reservation_id}}', '{{%partners_advert_reservations_failure}}', 'reservation_id', '{{%partners_advert_reservations}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('{{%partners_advert_reservations_failure_user_id}}', '{{%partners_advert_reservations_failure}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%partners_advert_reservations_failure}}');
        $this->dropTable('{{%partners_advert_reservations_deal}}');
        $this->dropTable('{{%partners_advert_reservations}}');
        $this->dropTable('{{%partners_reservations_payment}}');
        $this->dropTable('{{%partners_reservations}}');
    }
}
