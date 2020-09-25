<?php

namespace common\modules\agency\models\query;

use yii\db\ActiveQuery;
use common\modules\agency\models\Apartment;

/**
 * Class Apartments Query
 * @package common\modules\agency\models\query
 */
class ApartmentsQuery extends ActiveQuery
{
    /**
     * @param int $state
     * @return $this
     */
    public function visible($state = 1)
    {
        $this->andWhere([Apartment::tableName() . '.visible' => $state]);

        return $this;
    }
}
