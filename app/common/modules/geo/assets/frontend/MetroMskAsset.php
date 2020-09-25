<?php

namespace common\modules\geo\assets\frontend;

use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 * @package common\modules\geo\assets\frontend
 */
class MetroMskAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@frontend/themes/basic/assets';

    /**
     * @inheritdoc
     */
    public $depends = [
        'frontend\themes\basic\assets\AppAsset'
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (YII_DEBUG) {
            $this->css = [
                'css_dev/metro-msk.css'
            ];

            $this->js = [
                'js_dev/metro-msk.js'
            ];
        } else {
            $this->css = [
                'css/metro-msk.css'
            ];

            $this->js = [
                'js/metro-msk.js'
            ];
        }
    }
}
