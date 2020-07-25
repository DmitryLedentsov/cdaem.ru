<?php

namespace backend\themes\basic\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 */
class InterfaceAssets extends AssetBundle
{
    public $sourcePath = '@backend/themes/basic/assets';

    public $js = [
        "js/plugins/charts/sparkline.min.js",
        "js/plugins/interface/datatables.min.js",
        "js/plugins/interface/daterangepicker.js",
        "js/plugins/interface/fancybox.min.js",
        "js/plugins/interface/moment.js",
        "js/plugins/interface/jgrowl.min.js",
        "js/plugins/interface/datatables.min.js",
        "js/plugins/interface/colorpicker.js",
        "js/plugins/interface/fullcalendar.min.js",
        "js/plugins/interface/timepicker.min.js",
        "js/plugins/interface/collapsible.min.js",
    ];

    public $depends = [
    ];

}