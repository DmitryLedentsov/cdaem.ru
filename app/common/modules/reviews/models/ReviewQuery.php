<?php

namespace common\modules\reviews\models;

use common\modules\reviews\models\Review;
use yii\db\ActiveQuery;
use yii;

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