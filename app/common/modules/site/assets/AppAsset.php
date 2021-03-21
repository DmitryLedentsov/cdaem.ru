<?php

namespace common\modules\site\assets;

use yii\web\AssetBundle;

/**
 * Class AppAsset
 * Менеджер ресурсов
 * @package common\modules\site\assets
 */
class AppAsset extends AssetBundle
{
    //public $sourcePath = '@common/modules/site/assets';
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'https://use.fontawesome.com/releases/v5.15.1/css/all.css',
        '/_new/vendor/bootstrap/bootstrap.min.css',
        '/_new/vendor/daterangepicker/daterangepicker.min.css',
        '/_new/vendor/datetimepicker/bootstrap-datetimepicker.min.css',
        '/_new/css/base.min.css',
        '/_new/css/interfaces.min.css',
    ];
    
    public $js = [
        '/_new/vendor/popper.min.js',
        '/_new/vendor/moment_ru.min.js',
        '/_new/vendor/bootstrap/bootstrap.min.js',
        '/_new/vendor/display-validation.min.js',
        '/_new/vendor/jquery.inputmask.min.js',
        '/_new/vendor/daterangepicker/daterangepicker.min.js',
        '/_new/vendor/datetimepicker/bootstrap-datetimepicker.min.js',
        '/_new/vendor/bootstrap-autocomplete.min.js',
        '/_new/js/interface.min.js',

        '/_new/js_dev/formApi.js'
    ];

    public $depends = [
        \yii\web\JqueryAsset::class,
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->sourcePath = __DIR__;
    }

    /**
     * Получить url адрес папки с ресурсами
     * @return string
     */
    public static function getAssetUrl()
    {
        $obj = new self();

        return \Yii::$app->assetManager->getPublishedUrl($obj->sourcePath);
    }
}
