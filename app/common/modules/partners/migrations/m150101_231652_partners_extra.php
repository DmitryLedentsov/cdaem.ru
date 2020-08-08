<?php

use yii\db\Schema;
use yii\db\Migration;

class m150101_231652_partners_extra extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%helpdesk}}', 'partners_advert_id', Schema::TYPE_INTEGER . ' DEFAULT NULL');

        $this->addForeignKey('{{%helpdesk_partners_advert_id}}', '{{%helpdesk}}', 'partners_advert_id', '{{%partners_adverts}}', 'advert_id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        return false;
    }
}
