<?php

namespace common\modules\partners\models\scopes;

use common\modules\partners\models\ReservationFailure;
use yii\db\ActiveQuery;
use Yii;

/**
 * This is the ActiveQuery class for [[\common\modules\partners\models\ReservationFailure]].
 *
 * @see \common\modules\partners\models\ReservationFailure
 */
class ReservationFailureQuery extends ActiveQuery
{
    /**
     * Îáðàáîòàííûå ñèñòåìîé
     * @param int $state
     * @return $this
     */
    public function processed($state = 1)
    {
        $this->andWhere([ReservationFailure::tableName() . '.processed' => $state]);
        return $this;
    }

    /**
     * Îáðàáîòàííûå àäìèíèñòðàòîðîì
     * @param int $state
     * @return $this
     */
    public function closed($state = 1)
    {
        $this->andWhere([ReservationFailure::tableName() . '.closed' => $state]);
        return $this;
    }

    /**
     * Ó êîòîðûõ íàñòóïèëî âðåìÿ ê îáðàáîòêå
     * @return $this
     */
    public function timeHasCome()
    {
        $this->andWhere(['<=', ReservationFailure::tableName() . '.date_to_process', date('Y-m-d H:i:s')]);
        return $this;
    }
}