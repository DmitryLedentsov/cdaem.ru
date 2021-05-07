<?php

use yii\db\Migration;

/**
 * Class m210507_181177_helpdesk_user_phone
 */
class m210507_181177_helpdesk_user_phone extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%helpdesk}}', 'phone', 'varchar(255) DEFAULT null COMMENT "Телефон" AFTER `email` ');
    }

    public function safeDown()
    {
        $this->dropColumn('{{%helpdesk}}', 'phone');
    }
}
