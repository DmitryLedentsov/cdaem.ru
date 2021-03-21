<?php

namespace common\modules\partners\assets\frontend;

use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 * @package common\modules\partners\assets\frontend
 */
class ApartmentAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
//    public $sourcePath = '@frontend/themes/basic/assets';
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        '/_new/css/pages/fastrent.min.css',
    ];

    public $js = [
        '/_new/js/pages/fastrent.min.js'
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        \yii\web\JqueryAsset::class,
        \common\modules\site\assets\AppAsset::class,
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

//        if (YII_DEBUG) {
//            $this->js = [
//                '/_new/js_dev/partners.js'
//            ];
//        } else {
//            $this->js = [
//                '/_new/js_dev/partners.js'
//            ];
//        }
    }
}
