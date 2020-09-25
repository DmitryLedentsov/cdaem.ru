<?php

namespace common\modules\pages\models;

use yii\db\ActiveQuery;
use common\modules\pages\traits\ModuleTrait;

/**
 * Page Query
 * @package \common\modules\pages\models\query
 */
class PageQuery extends ActiveQuery
{
    use ModuleTrait;

    /**
     * @param int $state
     * @return $this
     */
    public function status($state = Page::STATUS_ALL)
    {
        $this->andWhere([Page::tableName() . '.status' => $state]);

        return $this;
    }
}
