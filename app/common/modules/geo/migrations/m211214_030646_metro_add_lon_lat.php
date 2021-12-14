<?php

use yii\db\Migration;

/**
 * Class m211214_030646_metro_add_lon_lat
 */
class m211214_030646_metro_add_lon_lat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%metro%}}', 'latitude', $this->decimal(17, 14)->null());
        $this->addColumn('{{%metro%}}', 'longitude', $this->decimal(17, 14)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%metro%}}', 'latitude');
        $this->dropColumn('{{%metro%}}', 'longitude');
    }
}
