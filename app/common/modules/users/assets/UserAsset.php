<?php

namespace common\modules\users\assets;

use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 * @package common\modules\users\assets
 */
class UserAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
//    public $sourcePath = '@frontend/themes/basic/assets';
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [
        '/_new/css/pages/signIn.min.css',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        \yii\web\JqueryAsset::class,
    ];
}
