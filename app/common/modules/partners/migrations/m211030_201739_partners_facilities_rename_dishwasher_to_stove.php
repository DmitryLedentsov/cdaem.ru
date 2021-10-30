<?php

use yii\db\Migration;

/**
 * Class m211030_201739_partners_facilities_rename_dishwasher_to_stove
 */
class m211030_201739_partners_facilities_rename_dishwasher_to_stove extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand()->update(
            '{{%partners_facilities}}',
            ['alias' => 'stove', 'name' => 'Плита'],
            ['alias' => 'dishwasher']
        )->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand()->update(
            '{{%partners_facilities}}',
            ['alias' => 'dishwasher', 'name' => 'Посудомоечная машина'],
            ['alias' => 'stove']
        )->execute();
    }
}
