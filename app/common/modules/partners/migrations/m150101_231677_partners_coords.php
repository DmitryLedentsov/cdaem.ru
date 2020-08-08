<?php

use yii\db\Schema;
use yii\db\Migration;

class m150101_231677_partners_coords extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%partners_apartments}}', 'latitude', 'DECIMAL(17,14)  DEFAULT NULL');
        $this->addColumn('{{%partners_apartments}}', 'longitude', 'DECIMAL(17,14)  DEFAULT NULL');
    }

    public function safeDown()
    {
        $this->dropColumn('{{%partners_apartments}}', 'latitude');
        $this->dropColumn('{{%partners_apartments}}', 'longitude');
    }
}
