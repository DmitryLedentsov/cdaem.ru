<?php

namespace common\modules\articles\models;

use yii\db\ActiveQuery;
use yii;

/**
 * Article Query
 * @package \common\modules\pages\models\query
 */
class ArticleQuery extends ActiveQuery
{
    /**
     * @param int $state
     * @return $this
     */
    public function status($state = Article::STATUS_ALL)
    {
        $this->andWhere([Article::tableName() . '.status' => $state]);
        return $this;
    }

}