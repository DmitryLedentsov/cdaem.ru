<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m160324_152350_seo_specifications_extra
 */
class m160324_152350_seo_specifications_extra extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%seo_specifications}}', 'service_head', Schema::TYPE_TEXT . ' DEFAULT NULL');
        $this->addColumn('{{%seo_specifications}}', 'service_footer', Schema::TYPE_TEXT . ' DEFAULT NULL');
    }

    public function safeDown()
    {
        return false;
    }
}
