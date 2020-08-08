<?php

use yii\db\Schema;

class m140508_233577_geo_cities_coords extends \yii\db\Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%city}}', 'latitude', 'DECIMAL(17,14)  DEFAULT NULL');
        $this->addColumn('{{%city}}', 'longitude', 'DECIMAL(17,14)  DEFAULT NULL');
    }

    public function safeDown()
    {
        $this->dropColumn('{{%city}}', 'latitude');
        $this->dropColumn('{{%city}}', 'longitude');
    }
}
