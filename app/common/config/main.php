<?php

return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'name' => 'CDAEM.RU',
    'timeZone' => 'Europe/Moscow',
    'charset' => 'utf-8',
    'language' => 'ru',
    'sourceLanguage' => 'ru-RU',
    'modules' => [
        'robotsTxt' => [
            'class' => 'execut\robotsTxt\Module',
            'components' => [
                'generator' => [
                    'class' => \execut\robotsTxt\Generator::class,
                    //or generate link through the url rules
                    'userAgent' => [
                        '*' => [
                            'Disallow' => [
                                ['/signup'],
                                ['/login'],
                                ['/*/login'],
                                ['/badbrowser'],
                                ['/resend'],
                                ['/call'],
                                ['/recovery'],
                                ['/page/agreement'],
                                ['/users/guest/resend'],
                                ['/fonts'],
                                ['/brw'],
                                ['/userpic'],
                                ['/pic'],
                                ['/imgs'],
                                ['/images-help'],
                                ['/uploads'],
                                ['/tmp'],
                                ['/ion.sound'],
                                ['/email-images'],
                                ['/reservation/*'],
                                ['/page/FAQ'],
                            ],
                            'Allow' => [
                                ['/partner_thumb'],
                                ['/partner_imgs'],
                                ['/images'],
                                ['/basic-images'],
                                ['/css'],
                                ['/*.css$'],
                                ['/*.js$'],
                                ['/js'],
                            ],
                        ],
                        'Googlebot' => [
                            'Disallow' => [
                                ['/signup'],
                                ['/login'],
                                ['/*/login'],
                                ['/badbrowser'],
                                ['/resend'],
                                ['/call'],
                                ['/recovery'],
                                ['/page/agreement'],
                                ['/users/guest/resend'],
                                ['/fonts'],
                                ['/brw'],
                                ['/userpic'],
                                ['/pic'],
                                ['/imgs'],
                                ['/images-help'],
                                ['/uploads'],
                                ['/tmp'],
                                ['/ion.sound'],
                                ['/email-images'],
                                ['/reservation/*'],
                                ['/page/FAQ'],
                            ],
                            'Allow' => [
                                ['/partner_thumb'],
                                ['/partner_imgs'],
                                ['/images'],
                                ['/basic-images'],
                                ['/css'],
                                ['/*.css$'],
                                ['/*.js$'],
                                ['/js'],
                            ],
                        ],
                        'Yandex' => [
                            'Disallow' => [
                                ['/signup'],
                                ['/login'],
                                ['/*/login'],
                                ['/badbrowser'],
                                ['/resend'],
                                ['/call'],
                                ['/recovery'],
                                ['/page/agreement'],
                                ['/users/guest/resend'],
                                ['/fonts'],
                                ['/brw'],
                                ['/userpic'],
                                ['/pic'],
                                ['/imgs'],
                                ['/images-help'],
                                ['/uploads'],
                                ['/tmp'],
                                ['/ion.sound'],
                                ['/email-images'],
                                ['/reservation/*'],
                                ['/page/FAQ'],
                            ],
                            'Allow' => [
                                ['/partner_thumb'],
                                ['/partner_imgs'],
                                ['/images'],
                                ['/basic-images'],
                                ['/css'],
                                ['/*.css$'],
                                ['/*.js$'],
                                ['/js'],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'components' => [
        'userAction' => [
            'class' => 'nepster\users\components\Action',
        ],
        'user' => [
            'class' => 'nepster\users\components\User',
            'identityClass' => 'common\modules\users\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['/users/guest/login'],
        ],
        'response' => [
            'class' => 'common\components\Response',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\DbTarget::class,
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => yii\log\DbTarget::class,
                    'levels' => ['info', 'error'],
                    'categories' => ['robokassa'],
                    // 'logFile' => '@frontend/runtime/logs/robokassa.log',
                    'logVars' => [],
                ],
            ],
        ],
        'session' => [
            'class' => 'yii\web\DbSession',
            'writeCallback' => function ($session) {
                return [
                    'user_id' => Yii::$app->user->id,
                ];
            },
            'readCallback' => function ($fields) {
                return [
                    'user_id' => $fields['user_id'],
                ];
            },
            'timeout' => 25 * 60, // сессия живет 25 минут
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'cache' => 'file-cache',
        ],
        'image' => [
            'class' => 'yii\image\ImageDriver',
            'driver' => 'GD',  //GD or Imagick
        ],
        'service' => [
            'class' => 'common\components\Service',
            'services' => [
                'partners' => [
                    'ADVERTISING_TOP_SLIDER' => '\common\modules\partners\services\AdvertisingTopSlider',
                    'ADVERTISING_IN_SECTION' => '\common\modules\partners\services\AdvertisingInSection',
                    'CONTACTS_OPEN_TO_USER' => '\common\modules\partners\services\ContactsOpenToUser',
                    'APARTMENT_CONTACTS_OPEN' => '\common\modules\partners\services\ApartmentContactsOpen',
                    'ADVERT_TOP_POSITION' => '\common\modules\partners\services\AdvertTopPosition',
                    'ADVERT_SELECTED' => '\common\modules\partners\services\AdvertSelected',
                    'ADVERT_IN_TOP' => '\common\modules\partners\services\AdvertInTop',
                    'CONTACTS_OPEN_FOR_TOTAL_BID' => '\common\modules\partners\services\ContactsOpenForTotalBid',
                    'CONTACTS_OPEN_FOR_RESERVATION' => '\common\modules\partners\services\ContactsOpenForReservation',
                ],
            ],
        ],
        'balance' => [
            'class' => 'common\modules\merchant\components\Balance',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'file-cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'BasisFormat' => [
            'class' => 'nepster\basis\components\BasisFormat',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'showScriptName' => false,
            'suffix' => '',
            'rules' => [
                ['pattern' => 'robots', 'route' => 'robotsTxt/web/index', 'suffix' => '.txt'],
            ]
        ],
        'assetManager' => [
            'linkAssets' => true
        ],
        'formatter' => [
            'as BasisBehavior' => new common\behaviors\BasisBehavior,
            'class' => 'common\components\Formatter',
            'dateFormat' => 'dd.MM.y',
            'datetimeFormat' => 'dd.MM.y в HH:mm:ss'
        ],
        'consoleRunner' => [
            'class' => 'vova07\console\ConsoleRunner',
            'file' => '@root/yii' // or an absolute path to console file
        ],
        'db' => require(__DIR__ . '/db.php'),
        'mailer' => require(__DIR__ . '/mailer.php'),
        'sms' => require(__DIR__ . '/sms.php'),
        'robokassa' => [
            'class' => '\nepster\robokassa\Api',
            'mrchLogin' => 'cdaemru',
            'mrchPassword1' => 'mojnL7Q2bk5c5JKWv3kH',
            'mrchPassword2' => 'F0K2maHn7VGx80UbbzKO',
            'resultUrl' => ['/merchant/robokassa/result'],
            'successUrl' => ['/merchant/robokassa/success'],
            'failureUrl' => ['/merchant/robokassa/failure']
        ],
        'currencyConverter' => [
            'class' => \common\components\CurrencyConverter::class,
        ],
        'reCaptcha' => require('reCaptcha.php'),
    ],
    'params' => require(__DIR__ . '/params.php')
];
