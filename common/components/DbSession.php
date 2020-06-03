<?php

namespace common\components;

use Yii;
use yii\db\Query;
use yii\web\Session;

/**
 * @inheritdoc
 */
class DbSession extends \yii\web\DbSession
{
    /**
     * @inheritdoc
     */
    public function regenerateID($deleteOldSession = false)
    {
        $oldID = session_id();

        // if no session is started, there is nothing to regenerate
        if (empty($oldID)) {
            return;
        }

        Session::regenerateID(false);
        $newID = session_id();

        $query = new Query;
        $row = $query->from($this->sessionTable)
            ->where(['id' => $oldID])
            ->createCommand($this->db)
            ->queryOne();
            
        if ($row !== false) {
            if ($deleteOldSession) {
                $this->db->createCommand()
                    ->update($this->sessionTable, ['id' => $newID, 'user_id' => Yii::$app->user->id], ['id' => $oldID])
                    ->execute();
            } else {
                $row['id'] = $newID;
                $this->db->createCommand()
                    ->insert($this->sessionTable, $row)
                    ->execute();
            }
        } else {
            // shouldn't reach here normally
            $this->db->createCommand()
                ->insert($this->sessionTable, [
                    'id' => $newID,
                    'expire' => time() + $this->getTimeout(),
                ])->execute();
        }
    }
}
