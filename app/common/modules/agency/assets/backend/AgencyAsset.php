<?php

namespace common\modules\agency\assets\backend;

use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 * @package common\modules\agency\assets\backend
 */
class AgencyAsset extends AssetBundle
{
    public $sourcePath = '@common/modules/agency/assets/backend';

    public $css = [
    ];

    public $js = [
        'js/control.js',
        'js/adverts.js',
        'js/advertisement.js',
        'js/specials.js',
    ];

    public $depends = [
        \yii\web\JqueryAsset::class,
        'yii\jui\JuiAsset',
    ];
}
