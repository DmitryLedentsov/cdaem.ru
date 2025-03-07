<?php

namespace common\modules\realty\models;

use yii\db\ActiveQuery;

/**
 * Rent Type Query
 * @package common\modules\realty\models
 */
class RentTypeQuery extends ActiveQuery
{
    /**
     * @param int $state
     * @return $this
     */
    public function visible($state = 1)
    {
        $this->andWhere([RentType::tableName() . '.visible' => $state]);

        return $this;
    }
}
