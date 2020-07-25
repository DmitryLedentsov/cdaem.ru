<?php

use yii\db\Migration;

class m150126_134981_agency_seo_short_desc extends Migration
{
    public function up()
    {
        $this->addColumn('{{%realty_rent_type}}', 'agency_seo_short_desc', 'text DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn('{{%realty_rent_type}}', 'agency_seo_short_desc');
    }
}
