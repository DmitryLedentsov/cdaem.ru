<?php

namespace common\modules\partners\models\frontend;

use yii\helpers\ArrayHelper;

/**
 * @inheritdoc
 */
class Advert extends \common\modules\partners\models\Advert
{
    /**
     * Текстовое представление цены для мета тега Title
     * @return string
     */
    public function getMetaTagsPrice()
    {
        return number_format($this->price, 0, '.', '') . ' ' . ArrayHelper::getValue($this->currencyList, $this->currency);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApartment()
    {
        return $this->hasOne(Apartment::class, ['apartment_id' => 'apartment_id']);
    }
}
