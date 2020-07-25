<?php

namespace backend\themes\basic\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 */
class BaseAsset extends AssetBundle
{
    public $sourcePath = '@backend/themes/basic/assets';

    public $js = [
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\jui\JuiAsset',
        'backend\themes\basic\assets\FormsAsset',
        'backend\themes\basic\assets\InterfaceAssets',
        'yii\bootstrap\BootstrapPluginAsset',
        'backend\themes\basic\assets\AppAsset',
    ];

}