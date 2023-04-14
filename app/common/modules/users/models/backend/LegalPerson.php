<?php

namespace common\modules\users\models\backend;

/**
 * @inheritdoc
 */
class LegalPerson extends \common\modules\users\models\LegalPerson
{
    /**
     * @inheritdoc
     */
    public function rules() : array
    {
        return [
            ['name', 'trim'],
            ['legal_address', 'trim'],
            ['physical_address', 'trim'],
            ['register_date', 'trim'],
            ['INN', 'trim'],
            ['PPC', 'trim'],
            ['account', 'trim'],
            ['bank', 'trim'],
            ['KS', 'trim'],
            ['BIK', 'trim'],
            ['BIN', 'trim'],
            ['director', 'trim'],
            ['description', 'string'],
            ['description', 'trim'],
        ];
    }
}
