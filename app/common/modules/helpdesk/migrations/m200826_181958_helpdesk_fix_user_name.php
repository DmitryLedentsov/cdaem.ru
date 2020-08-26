<?php

use yii\db\Migration;

/**
 * Class m200826_181958_helpdesk_fix_user_name
 */
class m200826_181958_helpdesk_fix_user_name extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('{{%helpdesk}}', 'user_name', 'varchar(255) DEFAULT NULL COMMENT "Имя неавторизированного пользователя"');
    }
}
