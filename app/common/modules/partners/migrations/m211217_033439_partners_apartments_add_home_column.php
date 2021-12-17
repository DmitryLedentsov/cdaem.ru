<?php

use yii\db\Migration;

/**
 * Class m211217_033439_partners_apartments_add_home_column
 */
class m211217_033439_partners_apartments_add_home_column extends Migration
{
    public static string $buildingType = 'building_type';
    public static string $numberEntrances = 'number_entrances';
    public static string $numberFloors = 'number_floors';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("{{%partners_apartments}}", self::$buildingType, $this->integer()->unsigned()->null()->comment('Тип здания'));
        $this->addColumn("{{%partners_apartments}}", self::$numberEntrances, $this->integer()->unsigned()->null()->comment('Количество подъездов'));
        $this->addColumn("{{%partners_apartments}}", self::$numberFloors, $this->integer()->unsigned()->null()->comment('Количество этажей'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("{{%partners_apartments}}", self::$buildingType);
        $this->dropColumn("{{%partners_apartments}}", self::$numberFloors);
        $this->dropColumn("{{%partners_apartments}}", self::$numberEntrances);
    }
}
