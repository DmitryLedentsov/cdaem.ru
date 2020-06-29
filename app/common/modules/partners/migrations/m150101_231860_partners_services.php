<?php

use yii\db\Schema;

/**
 * Class m150101_231860_partners_services
 */
class m150101_231860_partners_services extends \yii\db\Migration
{
    /**
     * @inheritdoc
     */
	public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // История заказов сервисов
        $this->createTable('{{%partners_services}}', [
            'id' => Schema::TYPE_PK,
            'service' => 'varchar(255) NOT NULL COMMENT "Сервис"',
            'payment_id' => Schema::TYPE_INTEGER,
            'user_id' => Schema::TYPE_INTEGER,
            'date_create' => 'datetime DEFAULT NULL COMMENT "Дата создания"',
            'date_start' => 'datetime DEFAULT NULL COMMENT "Дата запуска сервиса"',
            'date_expire' => 'datetime DEFAULT NULL COMMENT "Дата истечения"',
            'data' => 'text COMMENT "Данные для активации в JSON формате"',
            'process' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 COMMENT "Заявка обработана"',
        ], $tableOptions . ' COMMENT = "История заказов сервисов"');


        // Foreign Keys
        $this->addForeignKey('{{%partners_services_user_id}}', '{{%partners_services}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('{{%partners_services_payment_id}}', '{{%partners_services}}', 'payment_id', '{{%merchant_payments}}', 'payment_id', 'CASCADE', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
	public function safeDown()
	{
		$this->dropTable('{{%partners_services}}');
	}
}