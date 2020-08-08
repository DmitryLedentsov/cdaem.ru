<?php

namespace common\modules\users\models;

use yii\db\ActiveQuery;

/**
 * Class ProfileQuery
 */
class ProfileQuery extends ActiveQuery
{
    /**
     * @return \common\modules\users\models\ProfileQuery
     */
    public function partner($state = \common\modules\users\models\Profile::PARTNER)
    {
        return $this->andWhere([Profile::tableName() . '.user_partner' => $state]);
    }
}
