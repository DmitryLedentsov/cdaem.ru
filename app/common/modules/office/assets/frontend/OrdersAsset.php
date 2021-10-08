<?php

namespace common\modules\office\assets\frontend;

use yii\web\AssetBundle;

class OrdersAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [
        '/_new/css/pages/account/purchases.min.css',
    ];

    public $js = [

    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        OfficeAsset::class,
    ];
}
