<?php

namespace backend\modules\merchant\models;

/**
 * @inheritDoc
 * @package backend\modules\merchant\models
 */
class Payment extends \common\modules\merchant\models\Payment
{
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'user' => 'Пользователь'
        ]);
    }
}
