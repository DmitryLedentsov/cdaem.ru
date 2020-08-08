<?php

use yii\db\Schema;
use yii\db\Migration;
use nepster\users\helpers\Security;

/**
 * Create module tables.
 */
class m140418_204060_legal_person extends Migration
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

        // Пользователи
        $this->createTable('{{%users_legal_person}}', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'name' => Schema::TYPE_STRING . ' NULL DEFAULT NULL COMMENT "Название фирмы"',
            'legal_address' => Schema::TYPE_STRING . ' NULL DEFAULT NULL COMMENT "Юридический адрес"',
            'physical_address' => Schema::TYPE_STRING . ' NULL DEFAULT NULL COMMENT "Физический адрес"',
            'register_date' => Schema::TYPE_DATETIME . ' NULL DEFAULT NULL COMMENT "Дата регистрации"',
            'INN' => Schema::TYPE_STRING . ' NULL DEFAULT NULL COMMENT "ИНН"',
            'PPC' => Schema::TYPE_STRING . ' NULL DEFAULT NULL COMMENT "КПП"',
            'account' => Schema::TYPE_STRING . ' NULL DEFAULT NULL COMMENT "Расчетный счет"',
            'bank' => Schema::TYPE_STRING . ' NULL DEFAULT NULL COMMENT "Наименование банка"',
            'KS' => Schema::TYPE_STRING . ' NULL DEFAULT NULL COMMENT "К/с"',
            'BIK' => Schema::TYPE_STRING . ' NULL DEFAULT NULL COMMENT "Бик"',
            'BIN' => Schema::TYPE_STRING . ' NULL DEFAULT NULL COMMENT "ОГРН"',
            'director' => Schema::TYPE_STRING . ' NULL DEFAULT NULL COMMENT "Ген. директор"',
            'description' => Schema::TYPE_TEXT . ' NULL DEFAULT NULL COMMENT "Дополнительная информация"',
            'time_create' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL ',
            'time_update' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL ',
        ], $tableOptions . ' COMMENT = "Данные юридических лиц"');


        // Добавить колонку legal_person, указатель на юридическое лицо
        $this->addColumn('{{%users_profile}}', 'legal_person', Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 AFTER `birthday`');

        // Ключи
        $this->addForeignKey('{{%users_legal_person_user_id}}', '{{%users_legal_person}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%users_legal_person}}');
    }
}
