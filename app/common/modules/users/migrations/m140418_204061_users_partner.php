<?php

use yii\db\Schema;
use yii\db\Migration;
use nepster\users\helpers\Security;

/**
 * Добавление дополнительных полей и триггеров
 */
class m140418_204061_users_partner extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%users_profile}}', 'user_partner', Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 COMMENT "Пользователь партнер"');
        $this->addColumn('{{%users_profile}}', 'user_partner_rating', Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "Рейтинг партнера"');
        $this->addColumn('{{%users_profile}}', 'user_partner_verify', Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 COMMENT "Верифицированный партнер"');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%users_profile}}', 'user_partner');
        $this->dropColumn('{{%users_profile}}', 'user_partner_rating');
        $this->dropColumn('{{%users_profile}}', 'user_partner_verify');
    }
}
