<?php

namespace common\modules\users\models;

/**
 * Class ProfileQuery
 */
class UsersListQuery extends \yii\db\ActiveQuery
{
    /**
     * @return \common\modules\users\models\ProfileQuery
     */
    public function bookmarked($state = \common\modules\users\models\UsersList::BOOKMARK)
    {
        return $this->andWhere([UsersList::tableName() . '.type' => $state]);
    }
    
    /**
     * @return \common\modules\users\models\ProfileQuery
     */
    public function blacklisted($state = \common\modules\users\models\UsersList::BLACKLIST)
    {
        return $this->andWhere([UsersList::tableName() . '.type' => $state]);
    }
}