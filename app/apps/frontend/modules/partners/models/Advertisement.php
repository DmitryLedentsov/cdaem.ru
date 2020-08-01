<?php

namespace frontend\modules\partners\models;

use Yii;

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
