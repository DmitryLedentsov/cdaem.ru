<?php

namespace common\modules\office\assets\frontend;

use yii\web\AssetBundle;

/**
 * Class OfficeAsset
 * @package common\modules\office\assets\frontend
 */
class __OfficeAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@frontend/themes/basic/assets';

    /**
     * @inheritdoc
     */
    public $depends = [
        \yii\web\JqueryAsset::class,
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (YII_DEBUG) {
            $this->css = [
                'css_dev/office.css',
            ];

            $this->js = [
                'js_dev/office.js',
            ];
        } else {
            $this->css = [
                'css/office.css',
            ];

            $this->js = [
                'js/office.js',
            ];
        }
    }
}
