<?php

namespace frontend\modules\partners\models;


use frontend\modules\merchant\models\Service;
use yii\helpers\ArrayHelper;
use Yii;

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
