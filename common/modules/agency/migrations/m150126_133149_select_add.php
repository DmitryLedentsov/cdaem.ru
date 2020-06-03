<?php

use yii\db\Migration;

class m150126_133149_select_add extends Migration
{
    public function up()
    {
        $this->addColumn('{{%agency_apartment_select}}', 'date_create', 'datetime DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn('{{%agency_apartment_select}}', 'date_create');
    }
}
