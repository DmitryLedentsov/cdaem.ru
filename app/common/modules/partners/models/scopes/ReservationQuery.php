<?php

namespace common\modules\partners\models\scopes;

use Yii;
use yii\db\ActiveQuery;
use common\modules\partners\models\Reservation;

/**
 * Class ReservationQuery
 * @package common\modules\partners\models
 */
class ReservationQuery extends ActiveQuery
{
    /**
     * @param int $state
     * @return $this
     */
    public function closed($state = 1)
    {
        $this->andWhere([Reservation::tableName() . '.closed' => $state]);

        return $this;
    }

    /**
     * @param array $state
     * @return $this
     */
    public function cancel($state = [1, 2])
    {
        $this->andWhere([Reservation::tableName() . '.cancel' => $state]);

        return $this;
    }

    /**
     * @return $this
     */
    public function notClosedAndCancel()
    {
        $this->closed(0)->cancel(0);

        return $this;
    }

    /**
     * @return $this
     */
    public function actual()
    {
        $this->andWhere(['>=', 'date_actuality', date('Y-m-d H:i:s')]);

        return $this;
    }

    /**
     * Созданные за последние три месяца
     * @return $this
     */
    public function lastThreeMonths()
    {
        $date = new \DateTime('now');
        $date->sub(new \DateInterval('P3M'));
        $this->andWhere(['>=', 'date_create', $date->format('Y-m-d H:i:s')]);

        return $this;
    }

    /**
     * @return $this
     */
    public function thisUser()
    {
        $this->andWhere(['user_id' => Yii::$app->user->id]);

        return $this;
    }
}
