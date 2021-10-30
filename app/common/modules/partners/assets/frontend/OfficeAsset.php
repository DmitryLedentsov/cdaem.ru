<?php

namespace common\modules\partners\assets\frontend;

use yii\web\AssetBundle;

class OfficeAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [
        '/_new/css/pages/account/create-adv.min.css',
    ];

    public $js = [
        '/_new/js/pages/account/home.min.js',
        '/_new/js/pages/account/apartment.min.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        \common\modules\office\assets\frontend\OfficeAsset::class,
        \common\modules\geo\assets\frontend\YMapAsset::class
    ];
}

// <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=65a47f65-37ef-4724-b7c7-fba4b0cab20c" type="text/javascript"></script>
// <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js" integrity="sha256-u7e5khyithlIdTpu22PHhENmPcRdFiHRjhAuHcs05RI=" crossorigin="anonymous"></script>
