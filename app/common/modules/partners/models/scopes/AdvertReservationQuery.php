<?php

namespace common\modules\partners\models\scopes;

use Yii;
use yii\db\ActiveQuery;
use common\modules\partners\models\AdvertReservation;

/**
 * Advert Reservation Query
 * @package common\modules\partners\models\scopes
 */
class AdvertReservationQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function thisUser()
    {
        $this->andWhere([AdvertReservation::tableName() . '.user_id' => Yii::$app->user->id]);

        return $this;
    }

    /**
     * @return $this
     */
    public function thisLandlord()
    {
        $this->andWhere([AdvertReservation::tableName() . '.landlord_id' => Yii::$app->user->id]);

        return $this;
    }

    /**
     * @param int $state
     * @return $this
     */
    public function closed($state = 1)
    {
        $this->andWhere([AdvertReservation::tableName() . '.closed' => $state]);

        return $this;
    }

    /**
     * @param array $state
     * @return $this
     */
    public function cancel($state = [1, 2, 3])
    {
        $this->andWhere([AdvertReservation::tableName() . '.cancel' => $state]);

        return $this;
    }

    /**
     * @param array $state
     * @return $this
     */
    public function confirm($state = [1, 2, 3])
    {
        $this->andWhere([AdvertReservation::tableName() . '.confirm' => $state]);

        return $this;
    }

    /**
     * @return $this
     */
    public function actual()
    {
        $this->andWhere(['>', AdvertReservation::tableName() . 'date_actuality', date('Y-m-d H:i:s')]);

        return $this;
    }
}
