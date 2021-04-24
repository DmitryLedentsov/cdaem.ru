<?php

namespace common\modules\geo\assets\frontend;

use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 * @package common\modules\geo\assets\frontend
 */
class YMapAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
//    public $sourcePath = '@frontend/themes/basic/assets';
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    /**
     * @inheritdoc
     */
    public $depends = [
        \yii\web\JqueryAsset::class,
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (YII_DEBUG) {
            $this->js = [
                'https://api-maps.yandex.ru/2.1/?apikey=3dd27c27-e51a-4660-a191-5c0413af0c03&lang=ru_RU',
//                '/_new/js/scripts/ymap.js'
            ];
        } else {
            $this->js = [
                'https://api-maps.yandex.ru/2.1/?apikey=3dd27c27-e51a-4660-a191-5c0413af0c03&lang=ru_RU',
//                'js/ymap.js'
            ];
        }
    }
}
