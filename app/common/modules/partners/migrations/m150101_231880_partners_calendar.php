<?php

use yii\db\Schema;

/**
 * Class m150101_231880_partners_calendar
 */
class m150101_231880_partners_calendar extends \yii\db\Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // Календарь записей аренды апартаментов
        $this->createTable('{{%partners_calendar}}', [
            'id' => Schema::TYPE_PK,
            'apartment_id' => Schema::TYPE_INTEGER,
            'reserved' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1 COMMENT "Сейчас свободно"',
            'date_from' => 'datetime DEFAULT NULL COMMENT "Дата от"',
            'date_to' => 'datetime DEFAULT NULL COMMENT "Дата до"',
            'process' => $this->boolean()->notNull()->defaultValue(0),
        ], $tableOptions . ' COMMENT = "Календарь записей аренды апартаментов"');


        // Foreign Keys
        $this->addForeignKey('{{%partners_calendar_apartment_id}}', '{{%partners_calendar}}', 'apartment_id', '{{%partners_apartments}}', 'apartment_id', 'CASCADE', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%partners_calendar}}');
    }
}