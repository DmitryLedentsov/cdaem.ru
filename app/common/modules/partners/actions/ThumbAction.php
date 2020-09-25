<?php

namespace common\modules\partners\actions;

use Yii;
use yii\web\HttpException;

/**
 * @inheritdoc
 * @package common\modules\partners\actions
 */
class ThumbAction extends \iutbay\yii2imagecache\ThumbAction
{
    /**
     * @inheritdoc
     */
    public function run($path)
    {
        if (empty($path) || !Yii::$app->imageCacheAdverts->output($path)) {
            /*
            header("HTTP/1.0 404 Not Found");
            exit;
            */
            throw new HttpException(404, Yii::t('yii', 'Page not found.'));
        }
    }
}
