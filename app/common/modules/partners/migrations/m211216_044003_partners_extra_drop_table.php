<?php

use yii\db\Migration;

/**
 * Class m211216_044003_partners_extra_drop_table
 */
class m211216_044003_partners_extra_drop_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->db->getTableSchema('{{%partners_apartments_extras}}', true) !== null) {
            $this->dropTable('{{%partners_apartments_extras}}');
        }
        if ($this->db->getTableSchema('{{%partners_extras}}', true) !== null) {
            $this->dropTable('{{%partners_extras}}');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
