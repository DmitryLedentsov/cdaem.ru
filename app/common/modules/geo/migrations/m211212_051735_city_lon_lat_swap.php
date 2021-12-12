<?php

use yii\db\Migration;

/**
 * Class m211212_051735_city_lon_lat_swap
 */
class m211212_051735_city_lon_lat_swap extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('update {{%city}} set latitude = (@temp := latitude), latitude = longitude, longitude = @temp');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('update {{%city}} set longitude = (@temp := longitude), longitude = latitude, latitude = @temp');
    }
}
