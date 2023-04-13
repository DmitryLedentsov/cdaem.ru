<?php

namespace common\modules\partners\assets\frontend;

use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 * @package common\modules\partners\assets\frontend
 */
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
            'js_dev/add-in-slider.js',
            'js_dev/formApi.js',
            'js_dev/ui.js', // fast_payment_widget
            'widgets/scroll/jquery-scrolltofixed-min.js',
            'widgets/bootstrap-select/bootstrap-select.min.js'
        ];

        $this->css = [
            'widgets/bootstrap-select/dist/css/bootstrap-select.css',
            '/_new/css/pages/account/buy-ads.min.css' 
        ]; 

        /*
        if (YII_DEBUG) {
            $this->js = [
                'js_dev/add-in-slider.js',
            ];
        } else {
            $this->js = [
                'js/add-in-slider.js',
            ];
        }
        */
    }
}
