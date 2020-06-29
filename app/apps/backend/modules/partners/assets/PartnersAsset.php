<?php

namespace backend\modules\partners\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 */
class PartnersAsset extends AssetBundle
{
    public $sourcePath = '@backend/modules/partners/assets';

    public $css = [
    ];

    public $js = [
        'js/control.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\jui\JuiAsset',
    ];
}