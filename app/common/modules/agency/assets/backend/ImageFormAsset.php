<?php

namespace common\modules\agency\assets\backend;

use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 * @package common\modules\agency\assets\backend
 */
class ImageFormAsset extends AssetBundle
{
    public $sourcePath = '@common/modules/agency/assets/backend';

    public $css = [
    ];

    public $js = [
        'js/imageForm.js',
    ];

    public $depends = [
        \yii\web\JqueryAsset::class,
        'yii\jui\JuiAsset',
    ];
}
