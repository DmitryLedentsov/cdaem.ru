<?php

namespace backend\themes\basic\assets;

use Yii;
use yii\web\AssetBundle;
use yii\web\View;

/**
 * Менеджер ресурсов
 */
class FormsAsset extends AssetBundle
{
    public $sourcePath = '@backend/themes/basic/assets';

    public $css = [
    ];

    public $js = [
        'js/plugins/forms/uniform.min.js',
        'js/plugins/forms/select2.min.js',
        'js/plugins/forms/inputmask.js',
        'js/plugins/forms/autosize.js',
        'js/plugins/forms/inputlimit.min.js',
        'js/plugins/forms/listbox.js',
        'js/plugins/forms/multiselect.js',
        'js/plugins/forms/validate.min.js',
        'js/plugins/forms/tags.min.js',
        'js/plugins/forms/switch.min.js',
        'js/plugins/forms/uploader/plupload.full.min.js',
        'js/plugins/forms/uploader/plupload.queue.min.js',
        'js/plugins/forms/wysihtml5/wysihtml5.min.js',
        'js/plugins/forms/wysihtml5/toolbar.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}


  