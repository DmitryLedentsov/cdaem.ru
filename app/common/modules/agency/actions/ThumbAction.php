<?php

namespace common\modules\agency\actions;

use Yii;
use yii\web\NotFoundHttpException;

/**
 * @inheritdoc
 */
class ThumbAction extends \iutbay\yii2imagecache\ThumbAction
{
    /**
     * @inheritdoc
     */
    public function run($path)
    {
        if (empty($path) || !Yii::$app->imageCacheAgency->output($path)) {
            throw new NotFoundHttpException;
        }
    }
}
