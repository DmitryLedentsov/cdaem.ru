<?php

namespace frontend\themes\basic\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 * @package frontend\themes\basic\assets
 */
class DateTimeAsset extends AssetBundle
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
            'widgets/bootstrap-date/bootstrap-datetimepicker.css',
        ];


        if (YII_DEBUG) {
            $this->js = [
                'widgets/bootstrap-date/moment-with-locales.js',
                'widgets/bootstrap-date/bootstrap-datetimepicker.js',
            ];
        } else {
            $this->js = [
                'widgets/bootstrap-date/bootstrap-datetimepicker.min.js',
            ];
        }
    }
}
