<?php

namespace common\modules\partners\models\frontend;

use Yii;

/**
 * @inheritdoc
 */
class AdvertReservation extends \common\modules\partners\models\AdvertReservation
{
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdvert()
    {
        return $this->hasOne(Advert::class, ['advert_id' => 'advert_id']);
    }

    /**
     * @param null $myid
     * @return array|\yii\db\ActiveRecord|null
     */
    public function userothreservarion($myid = null)
    {
        $reservationwidth = AdvertReservation::find()
            ->Where(['!=', 'landlord_id', Yii::$app->user->id])
            ->andWhere(['confirm' => 2])
            ->andWhere(['user_id' => $myid])->one();

        return $reservationwidth;
    }
}
