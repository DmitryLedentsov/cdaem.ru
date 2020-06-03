<?php

namespace frontend\components;

use yii\web\Response;
use yii\helpers\Url;
use Yii;

/**
 * Общий контроллер
 * @package frontend\components
 */
class Controller extends \yii\web\Controller
{
    /**
     * @inheritdoc
     */    
    public $layout = false;

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        include_once(Yii::getAlias('@common/config/clickfrogru_udp_tcp.php'));

        return true;
    }

    /**
     * TODO: IE BUG REDIRECT
     * ВНИМАНИЕ:
     * На момент написания сайта был баг с 302 редиректом почти во всех браузерах IE:
     * https://github.com/yiisoft/yii2/issues/9670
     *
     * @inheritdoc
     */
    public function refresh($anchor = '')
    {
        return Yii::$app->getResponse()->redirect(Yii::$app->getRequest()->getUrl() . $anchor, 308);
    }
}
