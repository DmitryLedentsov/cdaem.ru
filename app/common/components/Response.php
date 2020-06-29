<?php

namespace common\components;

use yii\helpers\Url;
use yii;

/**
 * Response
 * @package common\components
 */
class Response extends \yii\web\Response
{
    /**
     * TODO: IE BUG REDIRECT
     * ВНИМАНИЕ:
     * На момент написания сайта был баг с 302 редиректом почти во всех браузерах IE:
     * https://github.com/yiisoft/yii2/issues/9670
     *
     * @inheritdoc
     */
    public function redirect($url, $statusCode = 302, $checkAjax = true)
    {
        if (Yii::$app->getRequest()->getIsAjax()) {
            if ($statusCode == 302) {
                $statusCode = 308;
            }
        }
        return parent::redirect($url, $statusCode, $checkAjax);
    }
}