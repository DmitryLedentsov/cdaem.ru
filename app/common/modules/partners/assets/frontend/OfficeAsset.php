<?php

namespace common\modules\partners\assets\frontend;

use yii\web\AssetBundle;

class OfficeAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [
        '/_new/css/pages/account/create-adv.min.css',
    ];

    public $js = [
        '/_new/js/pages/account/home.min.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        \common\modules\office\assets\frontend\OfficeAsset::class,
    ];
}
