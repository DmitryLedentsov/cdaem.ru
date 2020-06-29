<?php

namespace backend\modules\merchant\models;

use Yii;

/**
 * @inheritdoc
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
