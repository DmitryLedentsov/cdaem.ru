<?php

namespace frontend\modules\partners\models;

use Yii;

/**
 * @inheritdoc
 */
class MetroStations extends \common\modules\partners\models\MetroStations
{
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApartment()
    {
        return $this->hasOne(Apartment::class, ['apartment_id' => 'apartment_id']);
    }
}
