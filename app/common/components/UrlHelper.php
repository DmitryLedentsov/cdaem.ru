<?php

namespace common\components;

use Yii;
use yii\helpers\BaseUrl;

class UrlHelper extends BaseUrl
{
    public static function path($path, $args = [])
    {
        if (is_array($path)) {
            $path = array_merge($path, $args);
        } elseif ($args !== []) {
            $path = array_merge([$path], $args);
        }

        return self::to($path);
    }

    public static function getSiteDomain()
    {
        return Yii::$app->params['siteDomain'];
    }

    protected static function getUrlManager(): \yii\web\UrlManager
    {
        return Yii::$app->getUrlManager();
    }
}
