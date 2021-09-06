<?php

use yii\db\Migration;

/**
 * Class m150101_231661_partners_reservations_conflict
 *
 * Поле конфликт обозначает, что оба пользователя нажали незаезд и у них возник конфликт
 */
class m150101_231661_partners_reservations_conflict extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%partners_advert_reservations_failure}}', 'conflict', 'tinyint DEFAULT 0 COMMENT "Конфликт"');
    }

    public function safeDown()
    {
        $this->dropColumn('{{%partners_advert_reservations_failure}}', 'conflict');
    }
}
