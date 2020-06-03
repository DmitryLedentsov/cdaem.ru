<?php

use yii\db\Schema;
use yii\db\Migration;

class m150801_184527_triggers_seo extends Migration
{
    private $_table = 'seo_text';

    public function safeUp()
    {
        // Triggers
        $this->insert('{{%tables_update_time%}}', [
            'table' => $this->_table
        ]);

        $this->execute("
            CREATE TRIGGER {{%{$this->_table}_after_insert}} AFTER INSERT ON {{%{$this->_table}}}
            FOR EACH ROW
            BEGIN
            UPDATE {{%tables_update_time%}} SET update_time = UNIX_TIMESTAMP()
            WHERE `table` = '{$this->_table}';
            END
        ");

        $this->execute("
            CREATE TRIGGER {{%{$this->_table}_after_update}} AFTER UPDATE ON {{%{$this->_table}}}
            FOR EACH ROW
            BEGIN
            UPDATE {{%tables_update_time%}} SET update_time = UNIX_TIMESTAMP()
            WHERE `table` = '{$this->_table}';
            END
        ");

        $this->execute("
            CREATE TRIGGER {{%{$this->_table}_after_delete}} AFTER DELETE ON {{%{$this->_table}}}
            FOR EACH ROW
            BEGIN
            UPDATE {{%tables_update_time%}} SET update_time = UNIX_TIMESTAMP()
            WHERE `table` = '{$this->_table}';
            END
        ");
    }

    public function safeDown()
    {
        $this->execute("
            DROP TRIGGER IF EXISTS {{%{$this->_table}_after_insert}}
        ");
        $this->execute("
            DROP TRIGGER IF EXISTS {{%{$this->_table}_after_update}}
        ");
        $this->execute("
            DROP TRIGGER IF EXISTS {{%{$this->_table}_after_delete}}
        ");
    }
}
