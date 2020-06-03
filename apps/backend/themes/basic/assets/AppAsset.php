<?php

namespace backend\themes\basic\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 */
class AppAsset extends AssetBundle
{
	public $sourcePath = '@backend/themes/basic/assets';
    
	public $css = [
        'css/londinium-theme.css',
        'css/styles.css',
        'css/icons.css',
        'css/admin.css',
    ];
        
	public $js = [
        "js/application.js",
    ];
    
	public $depends = [
        'yii\web\JqueryAsset',
        'yii\jui\JuiAsset',
    ];

}