<?php

namespace common\modules\partners\models\scopes;

use yii\db\ActiveQuery;
use common\modules\partners\models\Service;

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
