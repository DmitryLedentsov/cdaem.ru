<?php

namespace frontend\modules\partners\models;

use Yii;

/**
 * @inheritdoc
 */
class AdvertisementSlider extends \common\modules\partners\models\AdvertisementSlider
{
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdvert()
    {
        return $this->hasOne(Advert::class, ['advert_id' => 'advert_id']);
    }
}
