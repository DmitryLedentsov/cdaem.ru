<?php

namespace common\modules\partners\models\frontend;

use Exception;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * @inheritdoc
 */
class Advert extends \common\modules\partners\models\Advert
{
    /**
     * Текстовое представление цены для мета тега Title
     * @return string
     * @throws Exception
     */
    public function getMetaTagsPrice(): string
    {
        return number_format($this->price, 0, '.', '') . ' ' . ArrayHelper::getValue($this->currencyList, $this->currency);
    }

    /**
     * @return ActiveQuery
     */
    public function getApartment(): ActiveQuery
    {
        return $this->hasOne(Apartment::class, ['apartment_id' => 'apartment_id']);
    }
}
