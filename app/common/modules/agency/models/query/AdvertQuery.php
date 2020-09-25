<?php

namespace common\modules\agency\models\query;

use yii\db\ActiveQuery;
use common\modules\agency\models\Advert;

/**
 * Advert Query
 * @package common\modules\agency\models\query
 */
class AdvertQuery extends ActiveQuery
{
    /**
     * @param int $state
     * @return $this
     */
    public function mainPage($state = 1)
    {
        return $this->andWhere([Advert::tableName() . '.main_page' => $state]);
    }
}
