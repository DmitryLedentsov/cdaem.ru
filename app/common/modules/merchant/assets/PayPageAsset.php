<?php

namespace common\modules\merchant\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 */
class PayPageAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
//    public $sourcePath = '@frontend/themes/basic/assets';
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [
        '/_new/css/pages/account/pay.min.css',
    ];

    public $js = [
        '/_new/js/pages/account/pay.min.js',
    ];
}
