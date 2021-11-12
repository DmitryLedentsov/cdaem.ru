<?php

use yii\db\Migration;

/**
 * Class m211112_211130_partners_apartments_extras
 */
class m211112_211130_partners_apartments_extras extends Migration
{
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

        $this->createTable('{{%partners_extras}}', [
            'extra_id' => $this->primaryKey(),
            'alias' => $this->string(255),
            'name' => $this->string(255)->notNull(),
            'is_active' => 'tinyint NOT NULL DEFAULT 1 COMMENT "Активный"',
            'date_create' => $this->dateTime()->notNull()->comment("Дата создания")->defaultExpression('NOW()'),
            'date_update' => $this->dateTime()->notNull()->comment("Дата редактирования")->defaultExpression('NOW()')
        ], $tableOptions . ' COMMENT = "Дополнительные параметры апартаментов"');

        $this->createIndex('index_partners_extras_alias', '{{%partners_extras}}', 'alias', true);

        $this->createTable('{{%partners_apartments_extras}}', [
            'extra_id' => $this->integer(),
            'apartment_id' => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey('fk-partners_apartments_extras_extra_id', '{{%partners_apartments_extras}}', 'extra_id', '{{%partners_extras}}', 'extra_id', 'CASCADE');
        $this->addForeignKey('fk-partners_apartments_extras_apartment_id', '{{%partners_apartments_extras}}', 'apartment_id', '{{%partners_apartments}}', 'apartment_id', 'CASCADE');

        Yii::$app->db->createCommand()->batchInsert(
            '{{%partners_extras}}',
            ['alias', 'name', 'is_active'],
            [
                ['parking', 'Парковка', 1],
                ['safety', 'Безопасность', 1],
                ['balcony', 'Балкон', 1],
                ['toilet', 'Санузел', 1],
                ['heating', 'Отопление', 1],
                ['floor-covering', 'Покрытие пола', 1],
            ]
        )->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%partners_apartments_extras}}');
        $this->dropTable('{{%partners_extras}}');
    }

}
