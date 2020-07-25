<?php

namespace common\modules\partners\models\scopes;

use common\modules\partners\models\Advert;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * Class AdvertQuery
 * @package common\modules\partners\models\scopes
 */
class AdvertQuery extends ActiveQuery
{
    /**
     * @param bool $state
     * @return $this
     */
    public function top($state = true)
    {
        if ($state == true) {
            $this->andWhere(Advert::tableName() . '.top = 1');
        } else {
            $this->andWhere(Advert::tableName() . '.top = 0');
        }

        return $this;
    }

    public function newyear($state = false)
    {
        if ($state == true) {
            $this->andWhere(Advert::tableName() . '.newyear = 1');
        } else {
            $this->andWhere(Advert::tableName() . '.newyear = 0');
        }

        return $this;
    }

    /**
     * @param bool $state
     * @return $this
     */
    public function selected($state = true)
    {
        if ($state == true) {
            $this->andWhere(Advert::tableName() . '.selected = 1');
        } else {
            $this->andWhere(Advert::tableName() . '.selected = 0');
        }

        return $this;
    }
}