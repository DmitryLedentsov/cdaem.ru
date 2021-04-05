<?php

namespace common\modules\articles\models;

use yii\db\ActiveQuery;

/**
 * Class ArticleQuery
 * @package common\modules\articles\models
 */
class ArticleQuery extends ActiveQuery
{
    /**
     * @param int $state
     * @return $this
     */
    public function status(int $state = Article::STATUS_ALL): self
    {
        if ($state !== null) {
            $this->andWhere([Article::tableName() . '.status' => $state]);
        }

        return $this;
    }

    /**
     * @param bool|null $state
     * @return $this
     */
    public function visible(?bool $state = true): self
    {
        if ($state !== null) {
            $this->andWhere([Article::tableName() . '.visible' => $state]);
        }

        return $this;
    }
}
