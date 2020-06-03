<?php

namespace common\modules\partners\models\scopes;

use common\modules\partners\models\Apartment;
use yii\db\ActiveQuery;

/**
 * Class ApartmentQuery
 * @package common\modules\partners\models
 */
class ApartmentQuery extends ActiveQuery
{
    /**
     * @param int $state
     * @return $this
     */
    public function visible($state = 1)
    {
        $this->andWhere([Apartment::tableName() . '.visible' => $state]);
        return $this;
    }

    /**
     * @param int $state
     * @return $this
     */
    public function available($state = 1)
    {
        $this->andWhere([Apartment::tableName() . '.now_available' => $state]);
        return $this;
    }

    /**
     * @param int $state
     * @return $this
     */
    public function status($state = 1)
    {
        $this->andWhere([Apartment::tableName() . '.status' => $state]);
        return $this;
    }

    /**
     * Разрешенный по двум условиям (status - админа и visible - пользователя) к показу на сайте
     * @return $this
     */
    public function permitted()
    {
        return $this->andWhere([
            Apartment::tableName() . '.visible' => 1,
            Apartment::tableName() . '.status' => Apartment::ACTIVE,
        ]);
    }
}