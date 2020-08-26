<?php

namespace common\modules\partners\models\frontend;

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
