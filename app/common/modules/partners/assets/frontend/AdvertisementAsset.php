<?php

namespace common\modules\partners\assets\frontend;

use yii\web\AssetBundle;

class AdvertisementAsset extends AssetBundle
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

        $this->js = [
            'js_dev/top-slider.js',
            'js_dev/formApi.js',
            'js_dev/ui.js', // fast_payment_widget
            'widgets/scroll/jquery-scrolltofixed-min.js',
            'widgets/bootstrap-select/bootstrap-select.min.js'
        ];

        $this->css = [
            'widgets/bootstrap-select/dist/css/bootstrap-select.css',
            '/_new/css/pages/account/top-slider.min.css' 
        ]; 

        /*
        if (YII_DEBUG) {
            $this->js = [
                'js_dev/top-slider.js',
            ];
        } else {
            $this->js = [
                'js/top-slider.js',
            ];
        }
        */
    }
}
