<?php

namespace common\modules\partners\assets\backend;

use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 */
class PartnersAsset extends AssetBundle
{
    public $sourcePath = '@common/modules/partners/assets/backend';

    public $css = [
    ];

    public $js = [
        'js/control.js',
    ];

    public $depends = [
        \yii\web\JqueryAsset::class,
        \yii\jui\JuiAsset::class,
    ];
}
