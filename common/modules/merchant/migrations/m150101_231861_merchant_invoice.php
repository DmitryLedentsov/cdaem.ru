<?php

use yii\db\Schema;

/**
 * Class m150101_231861_merchant_invoice
 */
class m150101_231861_merchant_invoice extends \yii\db\Migration
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

		// Денежный оборот
		$this->createTable('{{%merchant_invoice}}', [
            'invoice_id'   =>  Schema::TYPE_PK,
            'hash'         => Schema::TYPE_STRING,
            'user_id'      => 'int(11)',
            'process_id'   => Schema::TYPE_INTEGER,
            'system'       => 'varchar(255) NOT NULL COMMENT "Платежная система"',
            'funds'        => 'decimal(12,5) DEFAULT "0.00000" COMMENT "Средства"',
            'date_create'  => 'datetime NULL DEFAULT NULL',
            'date_payment' => 'datetime NULL DEFAULT NULL',
            'paid'         => Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT 0',
            'data'         => Schema::TYPE_TEXT,
        ], $tableOptions . ' COMMENT = "Счета"');

        // Index
        $this->createIndex('{{%merchant_invoice_hash}}', '{{%merchant_invoice}}', 'hash', true);

        // Foreign Keys
        $this->addForeignKey('{{%merchant_invoice_user_id}}', '{{%merchant_invoice}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('{{%merchant_process_id}}', '{{%merchant_invoice}}', 'process_id', '{{%partners_services}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
	public function safeDown()
	{
		$this->dropTable('{{%merchant_invoice}}');
	}
}