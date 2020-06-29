<?php

namespace common\modules\geo\assets\frontend;

use yii\web\AssetBundle;
use Yii;

/**
 * Менеджер ресурсов
 * @package common\modules\geo\assets\frontend
 */
class YMapAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@frontend/themes/basic/assets';

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\JqueryAsset',
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();


        if (YII_DEBUG) {

            $this->js = [
                'https://api-maps.yandex.ru/2.0-stable/?load=package.standard&amp;lang=ru-RU',
                'js_dev/ymap.js'
            ];

        } else {

            $this->js = [
                'https://api-maps.yandex.ru/2.0-stable/?load=package.standard&amp;lang=ru-RU',
                'js/ymap.js'
            ];
        }
    }
}