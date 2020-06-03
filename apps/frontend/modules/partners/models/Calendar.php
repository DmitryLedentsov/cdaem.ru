<?php

namespace frontend\modules\partners\models;

use Yii;

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
        return $this->hasOne(Apartment::className(), ['apartment_id' => 'apartment_id']);
    }
}