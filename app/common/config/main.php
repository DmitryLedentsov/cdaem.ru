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
            'class' => \execut\robotsTxt\Module::class,
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
        'user' => [
            'class' => \common\components\User::class,
            'identityClass' => \common\modules\users\models\User::class,
            'enableAutoLogin' => true,
            'loginUrl' => ['/users/guest/login'],
        ],
        'request' => [
            'class' => \common\components\Request::class,
        ],
        'response' => [
            'class' => \common\components\Response::class,
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
            'class' => \yii\web\DbSession::class,
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
            'class' => \yii\rbac\DbManager::class,
            'cache' => 'file-cache',
        ],
        'image' => [
            'class' => \yii\image\ImageDriver::class,
            'driver' => 'GD',  //GD or Imagick
        ],
        'service' => [
            'class' => \common\components\Service::class,
            'services' => [
                'partners' => [
                    'ADVERTISING_TOP_SLIDER' => \common\modules\partners\services\AdvertisingTopSlider::class,
                    'ADVERTISING_IN_SECTION' => \common\modules\partners\services\AdvertisingInSection::class,
                    'CONTACTS_OPEN_TO_USER' => \common\modules\partners\services\ContactsOpenToUser::class,
                    'APARTMENT_CONTACTS_OPEN' => \common\modules\partners\services\ApartmentContactsOpen::class,
                    'ADVERT_TOP_POSITION' => \common\modules\partners\services\AdvertTopPosition::class,
                    'ADVERT_SELECTED' => \common\modules\partners\services\AdvertSelected::class,
                    'ADVERT_IN_TOP' => \common\modules\partners\services\AdvertInTop::class,
                    'CONTACTS_OPEN_FOR_TOTAL_BID' => \common\modules\partners\services\ContactsOpenForTotalBid::class,
                    'CONTACTS_OPEN_FOR_RESERVATION' => \common\modules\partners\services\ContactsOpenForReservation::class,
                ],
            ],
        ],
        'balance' => [
            'class' => \common\modules\merchant\components\Balance::class,
        ],
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'file-cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'BasisFormat' => [
            'class' => \nepster\basis\components\BasisFormat::class,
        ],
        'urlManager' => [
            'class'=>\common\components\UrlManager::class,
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
            'class' => \common\components\Formatter::class,
            'dateFormat' => 'dd.MM.y',
            'datetimeFormat' => 'dd.MM.y в HH:mm:ss'
        ],
        'consoleRunner' => [
            'class' => \vova07\console\ConsoleRunner::class,
            'file' => '@root/yii' // or an absolute path to console file
        ],
        'db' => require(__DIR__ . '/db.php'),
        'mailer' => require(__DIR__ . '/mailer.php'),
        'sms' => require(__DIR__ . '/sms.php'),
        'robokassa' => require(__DIR__ . '/robokassa.php'),
        'currencyConverter' => [
            'class' => \common\components\CurrencyConverter::class,
        ],
        'reCaptcha' => require(__DIR__ . '/reCaptcha.php'),
    ],
    'params' => [
        'siteDomain' => 'http://cdaem.loc',
        'siteSubDomain' => 'http://<city>.cdaem.loc',
        'adminEmail' => 'cdaem@yandex.ru',
        'dadata' => [
            'token' => '79b777f05108f902a4019130a57fe5e7db725cc5',
            'secret' => 'b67ea1e4b91d9cc7af9a5f7be74f7c6d4803882a',
        ],
        'redirectableActions' => [
            'partners/default/view',
            'partners/default/others',
            'site/default/index',
            'geo'
        ]
    ]
];
