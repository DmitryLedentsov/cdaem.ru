<?php

namespace common\modules\users\assets;

use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 */
class UserAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [
        '/_new/css/pages/account/profile.min.css',
        '/_new/css/pages/auth.min.css',
    ];

    public $js = [
        '/_new/js/pages/account/profile.min.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        \yii\web\JqueryAsset::class,
    ];
}
