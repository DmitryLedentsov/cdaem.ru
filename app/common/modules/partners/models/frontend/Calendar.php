<?php

namespace common\modules\partners\models\frontend;

/**
 * @inheritdoc
 */
class Calendar extends \common\modules\partners\models\Calendar
{
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApartment()
    {
        return $this->hasOne(Apartment::class, ['apartment_id' => 'apartment_id']);
    }
}
