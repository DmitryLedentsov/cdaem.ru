<?php

use yii\db\Schema;
use yii\db\Migration;

class m150126_133277_agency_coords extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%agency_apartments}}', 'latitude', 'DECIMAL(17,14) DEFAULT NULL');
        $this->addColumn('{{%agency_apartments}}', 'longitude', 'DECIMAL(17,14) DEFAULT NULL');

    }

    public function safeDown()
    {
        $this->dropColumn('{{%agency_apartments}}', 'latitude');
        $this->dropColumn('{{%agency_apartments}}', 'longitude');
    }
}
