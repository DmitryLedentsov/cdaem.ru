<?php

namespace common\modules\agency\actions;

use yii\web\NotFoundHttpException;
use Yii;

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
