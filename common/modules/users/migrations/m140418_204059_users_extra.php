<?php

use nepster\users\helpers\Security;
use yii\db\Migration;
use yii\db\Schema;

/**
 * Добавление дополнительных полей
 */
class m140418_204059_users_extra extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%users_profile}}', 'advertising', Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1 COMMENT "Согласен получать рекламу"');

        $this->addColumn('{{%users_profile}}', 'second_name', Schema::TYPE_STRING . ' NULL DEFAULT NULL COMMENT "Фамилия"');
        $this->addColumn('{{%users_profile}}', 'phone2', Schema::TYPE_STRING . ' NULL DEFAULT NULL COMMENT "Второй телефон"');
        $this->addColumn('{{%users_profile}}', 'phones', Schema::TYPE_STRING . ' NULL DEFAULT NULL COMMENT "Список дополнительных телефонов"');
        $this->addColumn('{{%users_profile}}', 'user_type', Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1 COMMENT "Тип пользователя, на основе которого показываем личный кабинет"');
        $this->addColumn('{{%users_profile}}', 'vip', Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 COMMENT "VIP"');

        $this->addColumn('{{%users_profile}}', 'email', Schema::TYPE_STRING . ' NULL DEFAULT NULL COMMENT "Дополнительный почтовый адрес"');
        $this->addColumn('{{%users_profile}}', 'skype', Schema::TYPE_STRING . ' NULL DEFAULT NULL');
        $this->addColumn('{{%users_profile}}', 'ok', Schema::TYPE_STRING . ' NULL DEFAULT NULL');
        $this->addColumn('{{%users_profile}}', 'vk', Schema::TYPE_STRING . ' NULL DEFAULT NULL');
        $this->addColumn('{{%users_profile}}', 'fb', Schema::TYPE_STRING . ' NULL DEFAULT NULL');
        $this->addColumn('{{%users_profile}}', 'twitter', Schema::TYPE_STRING . ' NULL DEFAULT NULL');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%users_profile}}', 'advertising');

        $this->dropColumn('{{%users_profile}}', 'second_name');
        $this->dropColumn('{{%users_profile}}', 'phone2');
        $this->dropColumn('{{%users_profile}}', 'user_type');

        $this->dropColumn('{{%users_profile}}', 'email');
        $this->dropColumn('{{%users_profile}}', 'skype');
        $this->dropColumn('{{%users_profile}}', 'ok');
        $this->dropColumn('{{%users_profile}}', 'vk');
        $this->dropColumn('{{%users_profile}}', 'fb');
        $this->dropColumn('{{%users_profile}}', 'twitter');
    }
}
