<?php

namespace common\modules\partners\models\scopes;

use yii\db\ActiveQuery;
use common\modules\partners\models\AdvertisementSlider;

/**
 * Class AdvertisementSliderQuery
 * @package common\modules\partners\models\scopes
 */
class AdvertisementSliderQuery extends ActiveQuery
{
    /**
     * @param int $state
     * @return $this
     */
    public function visible($state = 1)
    {
        $this->andWhere([AdvertisementSlider::tableName() . '.visible' => $state]);

        return $this;
    }

    /**
     * @param int $state
     * @return $this
     */
    public function payment($state = 1)
    {
        $this->andWhere([AdvertisementSlider::tableName() . '.payment' => $state]);

        return $this;
    }
}
