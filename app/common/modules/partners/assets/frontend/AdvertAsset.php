<?php

namespace common\modules\partners\assets\frontend;

use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 * @package common\modules\partners\assets\frontend
 */
class AdvertAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
//    public $sourcePath = '@frontend/themes/basic/assets';
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        '/_new/css/pages/search.min.css',
    ];

    public $js = [
        '/_new/js/pages/search.min.js',
    ];

    public $depends = [
        \common\modules\site\assets\AppAsset::class,
    ];
}
