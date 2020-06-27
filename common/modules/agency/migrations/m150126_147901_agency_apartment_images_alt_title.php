<?php

use yii\db\Schema;
use yii\db\Migration;

class m150126_147901_agency_apartment_images_alt_title extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->addColumn('{{%agency_apartment_images}}', 'alt', 'varchar(255) DEFAULT NULL');
        $this->addColumn('{{%agency_apartment_images}}', 'title', 'varchar(255) DEFAULT NULL');

    }

    public function safeDown()
    {
        $this->dropColumn('{{%agency_apartment_images}}', 'alt');
        $this->dropColumn('{{%agency_apartment_images}}', 'title');
    }
}
