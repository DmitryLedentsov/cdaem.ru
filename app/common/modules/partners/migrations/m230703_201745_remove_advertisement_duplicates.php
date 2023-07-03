<?php

use yii\db\Migration;

/**
 * Class m230703_201745_remove_advertisement_duplicates
 */
class m230703_201745_remove_advertisement_duplicates extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->consoleRunner->run('partners/collector/remove-advertisement-duplicates');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230703_201745_remove_advertisement_duplicates cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230703_201745_remove_advertisement_duplicates cannot be reverted.\n";

        return false;
    }
    */
}
