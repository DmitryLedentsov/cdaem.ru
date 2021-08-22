<?php

namespace common\modules\office\assets\frontend;

use yii\web\AssetBundle;

/**
 * Class OfficeAsset
 * @package common\modules\office\assets\frontend
 */
class OfficeAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [
        '/_new/css/interfaces.min.css',
        '/_new/css/account/home.min.css',
    ];

    public $js = [
        '/_new/js/pages/account/home.min.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        \yii\web\JqueryAsset::class,
    ];
}
