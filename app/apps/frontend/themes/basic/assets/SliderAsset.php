<?php

namespace frontend\themes\basic\assets;

use yii\web\AssetBundle;
use Yii;

/**
 * Менеджер ресурсов
 * @package frontend\themes\basic\assets
 */
class SliderAsset extends AssetBundle
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

        $this->css = [
            'widgets/owl-carousel/owl-carousel.css',
        ];

        $this->js = [
            'widgets/owl-carousel/owl-carousel.min.js',
        ];
    }
}