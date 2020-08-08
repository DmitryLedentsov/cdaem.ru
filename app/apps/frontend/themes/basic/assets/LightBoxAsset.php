<?php

namespace frontend\themes\basic\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 * @package frontend\themes\basic\assets
 */
class LightBoxAsset extends AssetBundle
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

        $this->css = [
            'widgets/light-box/css/lightbox.css',
        ];

        if (YII_DEBUG) {
            $this->js = [
                'widgets/light-box/js/lightbox.js',
            ];
        } else {
            $this->js = [
                'widgets/light-box/js/lightbox.min.js',
            ];
        }
    }
}
