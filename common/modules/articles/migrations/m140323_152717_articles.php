<?php

use yii\db\Schema;

class m140323_152717_articles extends \yii\db\Migration
{
	public function safeUp()
	{
		$this->addColumn('{{%articles}}', 'city', 'varchar(128) DEFAULT NULL');
	}

	public function safeDown()
	{
		$this->dropColumn('{{%articles}}', 'city');
	}
}
