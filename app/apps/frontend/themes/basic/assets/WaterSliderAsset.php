<?php

namespace frontend\themes\basic\assets;

use yii\web\AssetBundle;
use Yii;

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
        'yii\web\JqueryAsset',
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
