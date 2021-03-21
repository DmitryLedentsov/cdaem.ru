<?php

namespace common\modules\helpdesk\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 * @package common\modules\helpdesk\assets
 */
class HelpdeskAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
//    public $sourcePath = '@frontend/themes/basic/assets';
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        '/_new/css/pages/help.min.css',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        \yii\web\JqueryAsset::class,
        \common\modules\site\assets\AppAsset::class,
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (YII_DEBUG) {
            $this->js = [
                '/_new/js_dev/helpdesk.js',
            ];
        } else {
            $this->js = [
                '/_new/js/helpdesk.js',
            ];
        }
    }
}
