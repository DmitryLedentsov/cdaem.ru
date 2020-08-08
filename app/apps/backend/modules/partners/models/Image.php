<?php

namespace backend\modules\partners\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @inheritdoc
 */
class Image extends \common\modules\partners\models\Image
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'ActionBehavior' => [
                'class' => 'nepster\users\behaviors\ActionBehavior',
                'module' => $this->module->id,
                'actions' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'create-partners-apartment-image',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'update-partners-apartment-image',
                    ActiveRecord::EVENT_BEFORE_DELETE => 'delete-partners-apartment-image',
                ],
            ],
        ];
    }
}
