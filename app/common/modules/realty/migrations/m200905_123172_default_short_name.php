<?php

use yii\db\Migration;

class m200905_123172_default_short_name extends Migration
{
    public function up()
    {
        $this->alterColumn('{{%realty_rent_type}}', 'short_name', 'varchar(255) DEFAULT null COMMENT "Короткое название"');
    }
}
