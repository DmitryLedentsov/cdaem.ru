<?php

namespace common\modules\partners\assets\frontend;

use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 * @package common\modules\partners\assets\frontend
 */
class RegionAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
//    public $sourcePath = '@frontend/themes/basic/assets';
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css',
        '/_new/css/pages/board.min.css',
    ];

    public $js = [
        'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js',
        '/_new/js/pages/home.min.js',
        '/_new/js/pages/board.min.js',
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

    }
}
