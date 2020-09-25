<?php

namespace common\modules\partners\models\frontend;

/**
 * @inheritdoc
 */
class Advertisement extends \common\modules\partners\models\Advertisement
{
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdvert()
    {
        return $this->hasOne(Advert::class, ['advert_id' => 'advert_id']);
    }
}
