<?php

namespace common\modules\agency\assets\frontend;

use yii\web\AssetBundle;

/**
 * Agency Asset
 * @package common\modules\agency\frontend\assets
 */
class AgencyAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
//    public $sourcePath = '@frontend/themes/basic/assets';
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [
        '/_new/css/pages/agency.min.css',
    ];

    public $js = [
        'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js',
        '/_new/js/pages/agency.min.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        \yii\web\JqueryAsset::class,
        'frontend\themes\basic\assets\DateTimeAsset',
        \common\modules\site\assets\AppAsset::class,
        \common\modules\pages\assets\PagesAsset::class,
    ];
}
