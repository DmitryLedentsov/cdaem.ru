<?php

namespace frontend\themes\basic\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 * @package frontend\themes\basic\assets
 */
class WaterSliderAsset extends AssetBundle
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
            'widgets/water-slider/wc.css',
        ];

        $this->js = [
            'widgets/water-slider/jquery.waterwheelCarousel.min.js',
            'widgets/water-slider/jquery.waterwheelCarousel.js',
            'widgets/water-slider/w-pattern.js',

        ];
    }
}
