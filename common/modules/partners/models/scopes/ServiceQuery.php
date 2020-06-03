<?php

namespace common\modules\partners\models\scopes;

use common\modules\partners\models\Service;
use yii\db\ActiveQuery;

/**
 * Class ServiceQuery
 * @package common\modules\partners\models\scopes
 */
class ServiceQuery extends ActiveQuery
{
    /**
     * @param int $state
     * @return $this
     */
    public function process($state = 0)
    {
        $this->andWhere([Service::tableName() . '.process' => $state]);
        return $this;
    }
}