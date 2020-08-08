<?php

namespace common\modules\agency\models\query;

use yii\db\ActiveQuery;
use common\modules\agency\models\Reservation;

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
