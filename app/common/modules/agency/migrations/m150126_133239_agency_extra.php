<?php

use yii\db\Schema;
use yii\db\Migration;

class m150126_133239_agency_extra extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        //Таблица специльных предложений
        $this->createTable('{{%agency_special_adverts}}', [
            'special_id' => Schema::TYPE_PK,
            'advert_id' => 'int NOT NULL COMMENT "ID объявления"',
            'text' =>  'varchar(255) COMMENT "Текст спец-предложения"',
            'date_create' => 'datetime NULL DEFAULT NULL COMMENT "Дата создания"',
            'date_start' => 'datetime NULL DEFAULT NULL COMMENT "Срок старта"',
            'date_expire' => 'datetime NULL DEFAULT NULL COMMENT "Срок истечения"',
        ], $tableOptions . ' COMMENT = "Специальные предложения"');
        
        // Foreign Keys
        $this->addForeignKey('FK_agency_special_adverts_advert_id', '{{%agency_special_adverts}}', 'advert_id', '{{%agency_adverts}}', 'advert_id', 'CASCADE', 'CASCADE');


        
        // Таблица рекламирования агентских апартаментов
        $this->createTable('{{%agency_advertisement}}', [
            'advertisement_id' => Schema::TYPE_PK,
            'advert_id' => 'int NOT NULL COMMENT "ID объявления"',
            'text' =>  'varchar(255) COMMENT "Текст рекламы"',
            'date_create' => 'datetime NULL DEFAULT NULL COMMENT "Дата создания"',
            'date_start' => 'datetime NULL DEFAULT NULL COMMENT "Дата старта"',
            'date_expire' => 'datetime NULL DEFAULT NULL COMMENT "Дата истечения"',
        ], $tableOptions . ' COMMENT = "Реклама агенства"');
        
        // Foreign Keys
        $this->addForeignKey('FK_agency_advertisement_advert_id', '{{%agency_advertisement}}', 'advert_id', '{{%agency_adverts}}', 'advert_id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%agency_special_adverts}}');
        $this->dropTable('{{%agency_advertisement}}');
    }
}
