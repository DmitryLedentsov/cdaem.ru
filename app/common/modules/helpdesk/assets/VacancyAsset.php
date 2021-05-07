<?php

namespace common\modules\helpdesk\assets;

use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 * @package common\modules\helpdesk\assets
 */
class VacancyAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
//    public $sourcePath = '@frontend/themes/basic/assets';
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        '/_new/css/pages/vacancy.min.css',
    ];

    public $js = [
        '/_new/js/pages/vacancy.min.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        \yii\web\JqueryAsset::class,
        \common\modules\site\assets\AppAsset::class,
        \common\modules\pages\assets\PagesAsset::class,
    ];
}
