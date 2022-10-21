<?php

use yii\db\Migration;

/**
 * Class m221021_203958_city_by_ip_cache
 */
class m221021_203958_city_by_ip_cache extends Migration
{
    static string $tableName = '{{%city_by_ip_cache}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(self::$tableName, [
           'ip' => $this->string(15)->notNull()->unique(),
           'city_id' => $this->integer()
        ]);

        $this->createIndex('{{%city_by_ip_cache_ip}}', self::$tableName, 'ip');
        $this->addForeignKey('{{%city_by_ip_cache_city_id}}', self::$tableName, 'city_id', '{{%city}}', 'city_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('{{%city_by_ip_cache_city_id}}', self::$tableName);
        $this->dropTable(self::$tableName);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221021_203958_city_by_ip_cache cannot be reverted.\n";

        return false;
    }
    */
}
