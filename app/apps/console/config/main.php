<?php

$params = array_merge(
    require(__DIR__ . '/../../../common/config/params.php'),
    require(__DIR__ . '/../../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'gii',
        'common\modules\users\Bootstrap',
        'frontend\modules\partners\Bootstrap',
    ],
    'controllerNamespace' => 'console\controllers',
    'modules' => [
        'gii' => 'yii\gii\Module',
        'users' => [
            'class' => 'common\modules\users\Module',
            'controllerMap' => [
                'control' => [
                    'class' => 'nepster\users\commands\ControlController',
                    'mailViewPath' => '@common/modules/users/mails/',
                ],
            ],
        ],
        'agency' => [
            'class' => 'common\modules\agency\Module',
            'controllerMap' => [
                'collector' => [
                    'class' => 'common\modules\agency\commands\CollectorController',
                ],
                'details' => [
                    'class' => 'common\modules\agency\commands\DetailsController',
                ],
            ],
        ],
        'partners' => [
            'class' => 'common\modules\partners\Module',
            'controllerMap' => [
                'collector' => [
                    'class' => 'common\modules\partners\commands\CollectorController',
                ],
                'reservation' => [
                    'class' => 'common\modules\partners\commands\ReservationController',
                ],
                'calendar' => [
                    'class' => 'common\modules\partners\commands\CalendarController',
                ],
            ],
        ],
        'merchant' => [
            'class' => 'common\modules\merchant\Module',
        ],
        'helpdesk' => [
            'class' => 'common\modules\partners\Module',
            'controllerNamespace' => 'common\modules\helpdesk\commands'
        ],
    ],
    'controllerMap' => [
        'migrate' => [
            'class' => 'console\controllers\MigrateController',
            'migrationLookup' => [
                '@common/modules/realty/migrations',
                '@common/modules/pages/migrations',
                '@nepster/users/migrations',
                '@nepster/users/migrations/extensions/session',
                '@yii/rbac/migrations',
                '@common/modules/callback/migrations',
                '@common/modules/helpdesk/migrations',
                '@common/modules/articles/migrations',
                '@common/modules/site/migrations',
                '@common/modules/agency/migrations',
                '@common/modules/partners/migrations',
                '@common/modules/merchant/migrations',
                '@common/modules/users/migrations',
                '@common/modules/reviews/migrations',
                '@common/modules/messages/migrations',
                '@common/modules/seo/migrations',
                '@common/modules/geo/migrations',
            ]
        ],
        'service' => [
            'class' => 'common\modules\partners\commands\ServicesController',
        ],
        'installer' => [
            'class' => 'nepster\modules\installer\Installer',
        ],
    ],
    'components' => [
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'suffix' => '',
            'baseUrl' => '',
        ],
        'log' => [
            'traceLevel' => 1,
            'targets' => [
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['info', 'error', 'warning'],
                    'categories' => ['users.mail'],
                    //'logFile' => '@console/runtime/logs/users.mail.log',
                    'logVars' => [],
                ],
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['info', 'error', 'warning'],
                    'categories' => ['services.processes'],
                    //'logFile' => '@console/runtime/logs/services.processes.log',
                    'logVars' => [],
                ],
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['info', 'error', 'warning'],
                    'categories' => ['services.calculate-position'],
                    //'logFile' => '@console/runtime/logs/services.calculate-position.log',
                    'logVars' => [],
                ],
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['info', 'error', 'warning'],
                    'categories' => ['services-email'],
                    //'logFile' => '@console/runtime/logs/services.email.log',
                    'logVars' => [],
                ],
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['info', 'error', 'warning'],
                    'categories' => ['reservation-email'],
                    //'logFile' => '@console/runtime/logs/reservation.email.log',
                    'logVars' => [],
                ],
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['info', 'error', 'warning'],
                    'categories' => ['reservation-sms'],
                    //'logFile' => '@console/runtime/logs/reservation.email.log',
                    'logVars' => [],
                ],
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['info', 'error', 'warning'],
                    'categories' => ['reservation-verify-payment'],
                    //'logFile' => '@console/runtime/logs/reservation.verify.payment.log',
                    'logVars' => [],
                ],
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['info', 'error', 'warning'],
                    'categories' => ['agency-cleaning-images'],
                    //'logFile' => '@console/runtime/logs/agency-collector.log',
                    'logVars' => [],
                ],
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['info', 'error', 'warning'],
                    'categories' => ['partners-cleaning-images'],
                    //'logFile' => '@console/runtime/logs/partners-collector.log',
                    'logVars' => [],
                ],
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['info', 'error', 'warning'],
                    'categories' => ['partners-calendar'],
                    //'logFile' => '@console/runtime/logs/partners-calendar.log',
                    'logVars' => [],
                ],
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['info', 'error', 'warning'],
                    'categories' => ['agency-details'],
                    //'logFile' => '@console/runtime/logs/agency-details.log',
                    'logVars' => [],
                ],
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['info', 'error', 'warning'],
                    'categories' => ['apartments-watcher'],
                    'logVars' => [],
                ],
            ],
        ],
    ],
    'params' => $params,
];
