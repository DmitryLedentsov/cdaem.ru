<?php

use yii\db\Schema;
use yii\db\Migration;

class m150801_184526_triggers extends Migration
{
    private $_tables = [
        'agency_apartment_select',
        'agency_apartment_reservations',
        'agency_adverts',
        'agency_apartment_metro_stations',
        'agency_apartment_images',
        'agency_apartments',
        'agency_special_adverts',
        'agency_advertisement',
        'agency_apartment_want_pass',
        'agency_details_history',
        'articles',
        'callback',
        'helpdesk_answers',
        'helpdesk',
        'merchant_payments',
        'merchant_invoice',
        'user_messages_mailbox',
        'user_messages',
        'pages',
        'partners_apartments',
        'partners_apartment_images',
        'partners_adverts',
        'partners_apartment_metro_stations',
        'partners_advertisement_slider',
        'partners_advertisement',
        'partners_reservations',
        'partners_reservations_payment',
        'partners_advert_reservations',
        'partners_advert_reservations_deal',
        'partners_advert_reservations_failure',
        'partners_services',
        'partners_calendar',
        'realty_rent_type',
        'reviews',
        'users',
        'users_profile',
        'users_list',
        'users_banned',
        'users_legal_person',
    ];

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // Tables update time
        $this->createTable('{{%tables_update_time}}', [
            'table' => 'varchar(255) NOT NULL PRIMARY KEY COMMENT "Имя таблицы"',
            'update_time' => 'int NOT NULL DEFAULT 0 COMMENT "Дата обновления"'
        ], $tableOptions . ' COMMENT = "Время последних обновлений всех таблиц"');

        foreach ($this->_tables as $table) {
            $this->createTriggers($table);
        }
    }

    public function safeDown()
    {
        $this->dropTable('{{%tables_update_time}}');

        foreach ($this->_tables as $table) {
            $this->deleteTriggers($table);
        }
    }

    private function createTriggers($table)
    {
        $this->deleteTriggers($table);

        // Triggers
        $this->insert('{{%tables_update_time%}}', [
            'table' => $table
        ]);

        $this->execute("
            CREATE TRIGGER {{%{$table}_after_insert}} AFTER INSERT ON {{%{$table}}}
            FOR EACH ROW
            BEGIN
            UPDATE {{%tables_update_time%}} SET update_time = UNIX_TIMESTAMP()
            WHERE `table` = '{$table}';
            END
        ");

        $this->execute("
            CREATE TRIGGER {{%{$table}_after_update}} AFTER UPDATE ON {{%{$table}}}
            FOR EACH ROW
            BEGIN
            UPDATE {{%tables_update_time%}} SET update_time = UNIX_TIMESTAMP()
            WHERE `table` = '{$table}';
            END
        ");

        $this->execute("
            CREATE TRIGGER {{%{$table}_after_delete}} AFTER DELETE ON {{%{$table}}}
            FOR EACH ROW
            BEGIN
            UPDATE {{%tables_update_time%}} SET update_time = UNIX_TIMESTAMP()
            WHERE `table` = '{$table}';
            END
        ");
    }

    private function deleteTriggers($table)
    {
        $this->execute("
            DROP TRIGGER IF EXISTS {{%{$table}_after_insert}}
        ");
        $this->execute("
            DROP TRIGGER IF EXISTS {{%{$table}_after_update}}
        ");
        $this->execute("
            DROP TRIGGER IF EXISTS {{%{$table}_after_delete}}
        ");
    }
}
