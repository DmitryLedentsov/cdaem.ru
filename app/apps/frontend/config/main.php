<?php

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        common\modules\site\Bootstrap::class,

        common\modules\helpdesk\BootstrapFrontend::class,

        common\modules\users\Bootstrap::class,
        common\modules\messages\Bootstrap::class,

        common\modules\pages\BootstrapFrontend::class,
        common\modules\articles\BootstrapFrontend::class,

        common\modules\callback\BootstrapFrontend::class,
        common\modules\reviews\BootstrapFrontend::class,

        common\modules\geo\BootstrapFrontend::class,

        common\modules\office\BootstrapFrontend::class,
        common\modules\merchant\BootstrapFrontend::class,
        common\modules\partners\BootstrapFrontend::class,

        common\modules\agency\BootstrapFrontend::class,
        common\modules\seo\BootstrapFrontend::class,
    ],
    'on beforeRequest' => function ($event) {
        if (Yii::$app->user) {
            $identity = Yii::$app->user->identity;
            if ($identity) {
                $identity->time_activity = time();
                $identity->save(false);
            }
        }

        // Настройки по умолчанию для виджета faceviewer
        \Yii::$container->set(\nepster\faceviewer\Widget::class, [
            'faceDefault' => 'no-avatar.png',
            'faceUrl' => Yii::$app->getModule('users')->avatarUrl,
            'facePath' => Yii::$app->getModule('users')->avatarPath,
            'faceUrlDefault' => '/basic-images',
            'userModel' => \common\modules\users\models\User::class,
            'userModelAttributes' => ['email', 'profile' => ['avatar_url', 'name', 'surname']],
        ]);
    },
    'defaultRoute' => 'site/default/index',
    'modules' => [
        'agency' => [
            'class' => common\modules\agency\Module::class,
            'controllerNamespace' => 'common\modules\agency\controllers\frontend',
        ],
        'helpdesk' => [
            'class' => common\modules\helpdesk\Module::class,
            'controllerNamespace' => 'common\modules\helpdesk\controllers\frontend',
        ],
        'site' => [
            'class' => common\modules\site\Module::class
        ],
        'users' => [
            'class' => common\modules\users\Module::class,
            'controllerNamespace' => 'common\modules\users\controllers\frontend',
        ],
        'messages' => [
            'class' => common\modules\messages\Module::class,
            'controllerNamespace' => 'common\modules\messages\controllers\frontend',
        ],
        'callback' => [
            'class' => common\modules\callback\Module::class,
            'controllerNamespace' => 'common\modules\callback\controllers\frontend',
        ],
        'reviews' => [
            'class' => common\modules\reviews\Module::class,
            'controllerNamespace' => 'common\modules\reviews\controllers\frontend',
        ],
        'pages' => [
            'class' => common\modules\pages\Module::class,
            'controllerNamespace' => 'common\modules\pages\controllers\frontend',
        ],
        'articles' => [
            'class' => common\modules\articles\Module::class,
            'controllerNamespace' => 'common\modules\articles\controllers\frontend',
        ],
        'geo' => [
            'class' => common\modules\geo\Module::class,
            'controllerNamespace' => 'common\modules\geo\controllers\frontend',
        ],
        'partners' => [
            'class' => common\modules\partners\Module::class,
            'controllerNamespace' => 'common\modules\partners\controllers\frontend',
        ],
        'office' => [
            'class' => common\modules\office\Module::class,
            'controllerNamespace' => 'common\modules\office\controllers\frontend',
        ],
        'merchant' => [
            'class' => common\modules\merchant\Module::class,
            'controllerNamespace' => 'common\modules\merchant\controllers\frontend',
        ],
    ],
    'components' => [

        /*'imageCacheAdverts' => [
            'class' => 'iutbay\yii2imagecache\ImageCache',
            'sourcePath' => '@frontend/web/partner_imgs',
            'sourceUrl' => '/partner_imgs',
            'thumbsPath' => '@frontend/web/partner_thumb',
            'thumbsUrl' => '/partner_thumb',
            'sizes' => [
                'advertsThumbs' => [300, 200],
            ],
            'sizeSuffixes' => [
                'advertsThumbs' => '',
            ],
        ],
        'imageCacheAgency' => [
            'class' => 'iutbay\yii2imagecache\ImageCache',
            'sourcePath' => '@frontend/web/images',
            'sourceUrl' => '/images',
            'thumbsPath' => '@frontend/web/images/thumbs',
            'thumbsUrl' => '/images/thumbs',
            'sizes' => [
                'agencyThumbs' => [300, 200],
            ],
            'sizeSuffixes' => [
                'agencyThumbs' => '.jpg_100x80',
            ],

        ],*/
        'inflection' => [
            'class' => 'wapmorgan\yii2inflection\Inflection'
        ],
        'language' => 'ru_RU',

        'request' => [
            'enableCsrfValidation' => true,
            'enableCookieValidation' => true,
            'baseUrl' => '',
            'as cityModelBehavior' => \common\behaviors\CityModelBehavior::class
        ],
        'view' => [
            'class' => \frontend\components\View::class,
            'defaultExtension' => 'twig',
            /*'theme' => [
                'pathMap' => [
                    '@app/views' => '@common/modules/site/',
                ],
                'baseUrl' => '@common/modules/site/',
            ],*/
            'theme' => [
                'pathMap' => [
                    '@app/views' => '@app/themes/basic',
                    '@app/modules' => '@app/themes/basic/modules',
                ],
                'baseUrl' => '@web/themes/basic',
            ],
            'renderers' => [
                'twig' => [
                    'class' => \yii\twig\ViewRenderer::class,
                    'cachePath' => '@runtime/Twig/cache',
                    'options' => [
                        'auto_reload' => true,
                    ],
                    'globals' => [
                        'DateTimeHelper' => ['class' => \common\components\DateTimeHelper::class],
                        'StatusHelper' => ['class' => \nepster\basis\helpers\StatusHelper::class],
                        'UserHelper' => ['class' => \common\modules\users\helpers\UserHelper::class],
                        'ArrayHelper' => ['class' => \yii\helpers\ArrayHelper::class],
                        'ApartmentHelper' => ['class' => \common\modules\partners\helpers\ApartmentHelper::class],
                        'CityHelper' => ['class' => \common\modules\partners\helpers\CityHelper::class],
                        'TotalApartment' => ['class' => \common\modules\realty\models\Apartment::class],
                        'html' => ['class' => \yii\helpers\Html::class],
                        'url' => ['class' => \common\components\UrlHelper::class],
                        //'pos_begin' => \yii\web\View::POS_BEGIN,
                        'callback' => new \common\modules\callback\models\Callback(),
                        'Service' => new \common\modules\partners\models\Service(),
                    ],
                    'functions' => [
                        't' => '\Yii::t',
                        'json_encode' => '\yii\helpers\Json::encode',
                        'dump' => '\yii\helpers\BaseVarDumper::dump',
                        'getAssetUrl' => '\frontend\themes\basic\assets\AppAsset::getAssetUrl',
                        'getPartOfTheDay' => '\common\modules\site\models\Total::getNamePartOfTheDay',
                        'strpos' => 'strpos',
                        'path'=>'\common\components\UrlHelper::path'
                    ],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/default/error',
        ],
        'assetManager' => [
            'basePath' => '@webroot/assets',
            'baseUrl' => '@web/assets',
            'bundles' => [
                \yii\web\JqueryAsset::class => [
                    'sourcePath' => null,
                    'js' => [
                        'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js',
                    ]
                ],
            ],
        ],
    ],
];
