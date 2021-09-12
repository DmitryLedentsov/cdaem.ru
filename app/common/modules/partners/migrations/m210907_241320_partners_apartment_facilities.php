<?php

use yii\db\Schema;
use yii\db\Migration;

class m210907_241320_partners_apartment_facilities extends Migration
{
    public function safeUp(): void
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // Apartment Reservations
        $this->createTable('{{%partners_facilities}}', [
            'facility_id' => Schema::TYPE_PK . ' COMMENT "№"',
            'alias' => 'varchar(255) DEFAULT NULL COMMENT "Slug"',
            'name' => 'varchar(255) DEFAULT NULL COMMENT "Название удобства"',
            'is_active' => 'tinyint NOT NULL DEFAULT 1 COMMENT "Активный"',
            'date_create' => 'datetime DEFAULT NULL COMMENT "Дата создания"',
            'date_update' => 'datetime DEFAULT NULL COMMENT "Дата редактирования"',
        ], $tableOptions . ' COMMENT = "Удобства в апартаментах"');

        $this->createIndex('index_partners_facilities_alias', '{{%partners_facilities}}', 'alias', true);


        $this->createTable('{{%partners_apartments_facilities}}', [
            'facility_id' => Schema::TYPE_INTEGER,
            'apartment_id' => Schema::TYPE_INTEGER,
        ], $tableOptions);

        $this->addForeignKey('fk-partners_apartments_facilities_facility_id', '{{%partners_apartments_facilities}}', 'facility_id', '{{%partners_facilities}}', 'facility_id', 'CASCADE');
        $this->addForeignKey('fk-partners_apartments_facilities_apartment_id', '{{%partners_apartments_facilities}}', 'apartment_id', '{{%partners_apartments}}', 'apartment_id', 'CASCADE');

        $datetime = (new DateTime('now'))->format('Y-m-d H:i:s');

        Yii::$app->db->createCommand()->batchInsert(
            '{{%partners_facilities}}',
            ['alias', 'name', 'is_active', 'date_create'],
            [
                ['refrigerator', 'Холодильник ', 1, $datetime],
                ['dishwasher', 'Посудомоечная машина', 1, $datetime],
                ['iron', 'Утюг', 1, $datetime],
                ['hair-dryer', 'Фен', 1, $datetime],
                ['wi-fi', 'WI-FI', 1, $datetime],
                ['microwave', 'Микроволновка', 1, $datetime],
                ['tv', 'Телевизор', 1, $datetime],
                ['air-conditioner', 'Кондиционер', 1, $datetime],
                ['washing-machine', 'Стиральная машина', 1, $datetime],
                ['sofa', 'Диван', 1, $datetime],
                ['bed', 'Кровать', 1, $datetime],
                ['shower', 'Душ', 1, $datetime],
                ['bath', 'Ванна', 1, $datetime],
                ['jacuzzi', 'Джакузи', 1, $datetime],
            ]
        )->execute();
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%partners_apartments_facilities}}');
        $this->dropTable('{{%partners_facilities}}');
    }
}
