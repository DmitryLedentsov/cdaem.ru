<?php

use yii\db\Migration;

/**
 * Class m211030_211515_city_add_column_is_popular
 */
class m211030_211515_city_add_column_is_popular extends Migration
{
    public static string $columnName = 'is_popular';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%city}}', self::$columnName, $this->tinyInteger()->notNull()->defaultValue(0)->after('name_eng'));

        Yii::$app->db->createCommand()->update(
            '{{%city}}',
            [self::$columnName => 1],
            ['name' => ['Москва',
                        'Санкт-Петербург',
                        'Новосибирск',
                        'Екатеринбург',
                        'Казань',
                        'Нижний Новгород',
                        'Челябинск',
                        'Самара']
            ]
        )->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%city}}', self::$columnName);
    }
}
