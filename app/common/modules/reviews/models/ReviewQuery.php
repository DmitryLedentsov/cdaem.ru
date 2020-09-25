<?php

namespace common\modules\reviews\models;

use yii\db\ActiveQuery;

/**
 * Review Query
 * @package common\modules\reviews\models
 */
class ReviewQuery extends ActiveQuery
{
    /**
     * @param int $state
     * @return $this
     */
    public function moderation($state = 1)
    {
        $this->andWhere([Review::tableName() . '.moderation' => $state]);

        return $this;
    }
}
