<?php

namespace common\modules\site\assets;

use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 * @package common\modules\site\assets
 */
class HomeAsset extends AssetBundle
{
    //public $sourcePath = '@common/modules/site/assets';
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        '/_new/css/pages/home.min.css',
    ];
    
    public $js = [
        '/_new/js/pages/home.min.js',
    ];

    public $depends = [
        \common\modules\site\assets\AppAsset::class,
    ];

    /**
     * Получить url адрес папки с ресурсами
     * @return string
     */
    public static function getAssetUrl(): string
    {
        $obj = new self();

        return \Yii::$app->assetManager->getPublishedUrl($obj->sourcePath);
    }
}
