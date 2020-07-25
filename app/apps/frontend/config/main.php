<?php

$params = array_merge(
    require(__DIR__ . '/../../../common/config/params.php'),
    require(__DIR__ . '/../../../common/config/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'frontend\modules\site\Bootstrap',

        'common\modules\helpdesk\BootstrapFrontend',

        'common\modules\users\Bootstrap',
        'common\modules\messages\Bootstrap',

        'common\modules\pages\BootstrapFrontend',
        'common\modules\articles\BootstrapFrontend',

        'common\modules\callback\BootstrapFrontend',
        'common\modules\reviews\BootstrapFrontend',

        'common\modules\geo\BootstrapFrontend',

        'frontend\modules\office\Bootstrap',
        'frontend\modules\merchant\Bootstrap',
        'frontend\modules\partners\Bootstrap',

        'common\modules\agency\BootstrapFrontend',
        'common\modules\seo\BootstrapFrontend',
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
        \Yii::$container->set('nepster\faceviewer\Widget', [
            'faceDefault' => 'no-avatar.png',
            'faceUrl' => Yii::$app->getModule('users')->avatarUrl,
            'facePath' => Yii::$app->getModule('users')->avatarPath,
            'faceUrlDefault' => '/basic-images',
            'userModel' => 'common\modules\users\models\User',
            'userModelAttributes' => ['email', 'profile' => ['avatar_url', 'name', 'surname']],
        ]);
    },
    'defaultRoute' => 'site/default/index',
    'modules' => [
        'agency' => [
            'class' => 'common\modules\agency\Module',
            'controllerNamespace' => 'common\modules\agency\controllers\frontend',
        ],
        'helpdesk' => [
            'class' => 'common\modules\helpdesk\Module',
            'controllerNamespace' => 'common\modules\helpdesk\controllers\frontend',
        ],
        'site' => [
            'class' => 'frontend\modules\site\Module'
        ],
        'users' => [
            'class' => 'common\modules\users\Module',
            'controllerNamespace' => 'common\modules\users\controllers\frontend',
        ],
        'messages' => [
            'class' => 'common\modules\messages\Module',
            'controllerNamespace' => 'common\modules\messages\controllers\frontend',
        ],
        'callback' => [
            'class' => 'common\modules\callback\Module',
            'controllerNamespace' => 'common\modules\callback\controllers\frontend',
        ],
        'reviews' => [
            'class' => 'common\modules\reviews\Module',
            'controllerNamespace' => 'common\modules\reviews\controllers\frontend',
        ],
        'pages' => [
            'class' => 'common\modules\pages\Module',
            'controllerNamespace' => 'common\modules\pages\controllers\frontend',
        ],
        'articles' => [
            'class' => 'common\modules\articles\Module',
            'controllerNamespace' => 'common\modules\articles\controllers\frontend',
        ],
        'geo' => [
            'class' => 'common\modules\geo\Module',
            'controllerNamespace' => 'common\modules\geo\controllers\frontend',
        ],

        'partners' => [
            'class' => 'frontend\modules\partners\Module',
        ],

        'office' => [
            'class' => 'frontend\modules\office\Module',
        ],
        'merchant' => [
            'class' => 'frontend\modules\merchant\Module',
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
        'view' => [
            'class' => '\frontend\components\View',
            'defaultExtension' => 'twig',
            'theme' => [
                'pathMap' => [
                    '@app/views' => '@app/themes/basic',
                    '@app/modules' => '@app/themes/basic/modules',
                ],
                'baseUrl' => '@web/themes/basic',
            ],
            'renderers' => [
                'twig' => [
                    'class' => 'yii\twig\ViewRenderer',
                    'cachePath' => '@runtime/Twig/cache',
                    'options' => [
                        'auto_reload' => true,
                    ],
                    'globals' => [
                        'DateTimeHelper' => ['class' => \nepster\basis\helpers\DateTimeHelper::class],
                        'StatusHelper' => ['class' => \nepster\basis\helpers\StatusHelper::class],
                        'UserHelper' => ['class' => \common\modules\users\helpers\UserHelper::class],
                        'ArrayHelper' => ['class' => \yii\helpers\ArrayHelper::class],
                        'ApartmentHelper' => ['class' => \frontend\modules\partners\helpers\ApartmentHelper::class],
                        'html' => ['class' => \yii\helpers\Html::class],
                        'url' => ['class' => \yii\helpers\Url::class],
                        'pos_begin' => \yii\web\View::POS_BEGIN,
                        'callback' => new \common\modules\callback\models\Callback(),
                        'Service' => new \common\modules\partners\models\Service(),
                    ],
                    'functions' => [
                        't' => '\Yii::t',
                        'json_encode' => '\yii\helpers\Json::encode',
                        'dump' => '\yii\helpers\BaseVarDumper::dump',
                        'getAssetUrl' => '\frontend\themes\basic\assets\AppAsset::getAssetUrl',
                        'getPartOfTheDay' => '\frontend\modules\site\models\Total::getNamePartOfTheDay',
                        'strpos' => 'strpos',
                    ],
                ],
            ],
        ],
        'request' => [
            'enableCsrfValidation' => true,
            'enableCookieValidation' => true,
            'baseUrl' => '',
            'as cityModelBehavior' => \common\behaviors\CityModelBehavior::class
        ],
        'log' => [
            'traceLevel' => 3,
            'targets' => [
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['info', 'error'],
                    'categories' => ['robokassa'],
                    // 'logFile' => '@frontend/runtime/logs/robokassa.log',
                    'logVars' => [],
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
                'yii\web\JqueryAsset' => [
                    'sourcePath' => null,
                    'js' => [
                        'https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js',
                    ]
                ],
            ],
        ],
        'currencyConverter' => [
            'class' => \common\components\CurrencyConverter::class,
        ],
        'reCaptcha' => require('reCaptcha.php'),
    ],
    'params' => $params,
];
