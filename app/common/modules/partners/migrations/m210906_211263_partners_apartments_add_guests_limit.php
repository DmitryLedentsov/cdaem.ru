<?php

use yii\db\Migration;

class m210906_211263_partners_apartments_add_guests_limit extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%partners_apartments}}', 'guests_limit', 'integer DEFAULT NULL COMMENT "Максимальное кол-во гостей"');
    }

    public function safeDown()
    {
        $this->dropColumn('{{%partners_apartments}}', 'guests_limit');
    }
}
