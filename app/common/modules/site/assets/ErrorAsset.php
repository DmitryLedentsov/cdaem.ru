<?php

namespace common\modules\site\assets;

use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 */
class ErrorAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [
        '/_new/css/pages/error.min.css',
    ];

    public $js = [];
}
