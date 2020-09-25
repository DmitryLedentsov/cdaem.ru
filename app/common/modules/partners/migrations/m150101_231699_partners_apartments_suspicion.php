<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m150101_231699_partners_apartments_suspicion
 */
class m150101_231699_partners_apartments_suspicion extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->addColumn('{{%partners_apartments}}', 'suspicious', Schema::TYPE_BOOLEAN . ' DEFAULT 0');
        $this->addColumn('{{%partners_apartments}}', 'suspicious_detect_date', 'datetime NULL COMMENT "Дата детекции подозрения"');
    }

    public function safeDown()
    {
        $this->dropColumn('{{%partners_apartments}}', 'suspicious');
        $this->dropColumn('{{%partners_apartments}}', 'suspicious_detect_date');
    }
}
