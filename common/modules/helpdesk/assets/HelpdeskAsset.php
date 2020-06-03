<?php

namespace common\modules\helpdesk\assets;

use yii\web\AssetBundle;
use Yii;

/**
 * Менеджер ресурсов
 * @package common\modules\helpdesk\assets
 */
class HelpdeskAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@frontend/themes/basic/assets';

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\JqueryAsset',
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (YII_DEBUG) {
            $this->js = [
                'js_dev/helpdesk.js',
            ];
        } else {
            $this->js = [
                'js/helpdesk.js',
            ];
        }
    }
}