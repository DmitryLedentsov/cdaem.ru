<?php

namespace common\modules\agency\models\query;

use common\modules\agency\models\Reservation;
use yii\db\ActiveQuery;

/**
 * Reservation Query
 * @package common\modules\agency\models\query
 */
class ReservationQuery extends ActiveQuery
{
    /**
     * @param int $state
     * @return $this
     */
    public function processed($state = 1)
    {
        return $this->andWhere([Reservation::tableName() . '.processed' => $state]);
    }
}