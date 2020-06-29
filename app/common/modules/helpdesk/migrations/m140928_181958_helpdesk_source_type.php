<?php

use yii\db\Migration;

/**
 * Class m140928_181958_helpdesk_source_type
 */
class m140928_181958_helpdesk_source_type extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%helpdesk}}', 'source_type', 'varchar(255) DEFAULT "agency" COMMENT "Раздел обращений"');
    }

    public function safeDown()
    {
        $this->dropColumn('{{%helpdesk}}', 'source_type');
    }
}
