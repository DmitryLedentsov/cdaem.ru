<?php

use yii\db\Schema;

class m150223_253832_reviews extends \yii\db\Migration
{
	public function safeUp()
	{
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // Отзывы
        $this->createTable('{{%reviews}}', [
            'review_id'         => Schema::TYPE_PK,
            'apartment_id'      => 'int NOT NULL',
            'user_id'           => 'int NOT NULL',
            'text'              => 'text  NOT NULL COMMENT "Текст отзыва"',
            'match_description' => 'tinyint(4) DEFAULT 1 COMMENT "Соответствие описанию"',
            'price_quality'     => 'tinyint(4) DEFAULT 1 COMMENT "Цена и качество"',
            'cleanliness'       => 'tinyint(4) DEFAULT 1 COMMENT "Чистота"',
            'entry'             => 'tinyint(4) DEFAULT 1 COMMENT "Заселение"',
            'date_create'       => 'datetime DEFAULT NULL COMMENT "Дата создания"',
            'moderation'        => 'tinyint(1) DEFAULT 0 COMMENT "Модерация"',
            'visible'           => 'tinyint(1) DEFAULT 1 COMMENT "Отображение на сайте"',
        ], $tableOptions . ' COMMENT = "Отзывы"');


        $this->createIndex('{{%reviews_apartment_id}}', '{{%reviews}}', 'apartment_id');
        $this->createIndex('{{%reviews_user_id}}', '{{%reviews}}', 'user_id');

        // Foreign Keys
        $this->addForeignKey('{{%reviews_apartment_id}}', '{{%reviews}}', 'apartment_id', '{{%partners_apartments}}', 'apartment_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('{{%reviews_user_id}}', '{{%reviews}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
	}

	public function safeDown()
	{
		$this->dropTable('{{%reviews}}');
	}
}
