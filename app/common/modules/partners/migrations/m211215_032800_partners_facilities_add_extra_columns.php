<?php

use yii\db\Migration;

/**
 * Class m211215_032800_partners_facilities_add_extra_columns
 */
class m211215_032800_partners_facilities_add_extra_columns extends Migration
{
    public static string $columnName = "is_extra";
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->addColumn('{{%partners_facilities}}', self::$columnName,
            $this->boolean()->notNull()->defaultValue(0)->comment('Дополнительное удобство')
        );

        $this->addColumn('{{%partners_apartments_facilities}}', 'value',
            $this->string(1500)->null()->comment('Значение с которым указано доп. удосбство (например, id покрытия пола или id типа отопления)')
        );

        $datetime = (new DateTime('now'))->format('Y-m-d H:i:s');

        Yii::$app->db->createCommand()->batchInsert(
            '{{%partners_facilities}}',
            ['alias', 'name', 'is_active', self::$columnName, 'date_create'],
            [
                ['parking', 'Парковка', 1, 1, $datetime],
                ['balcony', 'Балкон', 1, 1, $datetime],
                ['heating', 'Отопление', 1, 1, $datetime],
                ['separate-toilet', 'Раздельные санузлы', 1, 1, $datetime],
                ['toilet', 'Совмещённые санузлы', 1, 1, $datetime],
                ['floor-covering', 'Покрытие пола', 1, 1, $datetime],
                ['safety', 'Безопасность', 1, 1, $datetime],
            ]
        )->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%partners_facilities}}', 'is_extra = 1');
        $this->dropColumn('{{%partners_facilities}}', self::$columnName);
        $this->dropColumn('{{%partners_apartments_facilities}}', 'value');
    }

}
