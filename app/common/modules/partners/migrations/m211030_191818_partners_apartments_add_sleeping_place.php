<?php

use yii\db\Migration;

/**
 * Class m211030_191818_partners_apartments_add_sleeping_place
 */
class m211030_191818_partners_apartments_add_sleeping_place extends Migration
{
    public static string $columnName = 'sleeping_place';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%partners_apartments}}',
            self::$columnName,
            $this->tinyInteger()
                ->after('beds')
                ->comment('Кол-во спальных мест')
        );

        $this->addCommentOnColumn('{{%partners_apartments}}', 'beds', 'Кол-во кроватей');

        Yii::$app->db->createCommand()->update('{{%partners_apartments}}', [self::$columnName => new \yii\db\Expression('beds')])
            ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%partners_apartments}}', self::$columnName);
        $this->addCommentOnColumn('{{%partners_apartments}}', 'beds', 'Кол-во спальных мест');
    }
}
