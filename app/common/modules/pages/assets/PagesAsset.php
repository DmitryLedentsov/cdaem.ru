<?php

namespace common\modules\pages\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 * @package common\modules\pages\assets
 */
class PagesAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
//    public $sourcePath = '@frontend/themes/basic/assets';
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        '/_new/css/pages/static.min.css',
    ];

}
