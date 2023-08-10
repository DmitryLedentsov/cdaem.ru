<?php

use yii\db\Migration;
use common\modules\users\models\backend\User;

/**
 * Class m230406_204811_delete_users_with_incorrect_phone
 */
class m230406_204811_delete_users_with_incorrect_phone extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $db = Yii::$app->db;
        $userIdList = $db->createCommand("SELECT u.id, u.phone FROM users u where not u.phone regexp '^[78]{1}\\\d{10}$'")
            // ->rawSql;
            ->queryAll();

        $deleteCount = 0;
        foreach ($userIdList as $userRow) {
            $userId = $userRow['id'];
            // echo $userId . PHP_EOL;
            $user = User::findOne($userId);
            // var_dump($user);
            $user->status = $user::STATUS_DELETED;
            $apartmentsByUser = \common\modules\partners\models\Apartment::findApartmentsByUser($user->id);

            foreach ($apartmentsByUser as $apartment) {
                // $apartment->delete();
            }

            if (/*empty($apartmentsByUser) and*/ $user->status == $user::STATUS_DELETED) {
                $user->delete();
                $deleteCount++;
            }
        }
        echo $deleteCount . " users was deleted";
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230406_204811_delete_users_with_incorrect_phone cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230406_204811_delete_users_with_incorrect_phone cannot be reverted.\n";

        return false;
    }
    */
}
