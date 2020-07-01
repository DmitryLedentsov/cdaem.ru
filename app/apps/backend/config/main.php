<?php

$params = array_merge(
    require(__DIR__ . '/../../../common/config/params.php'),
    require(__DIR__ . '/../../../common/config/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'backend\modules\admin\Bootstrap',
        'common\modules\users\Bootstrap',
        'common\modules\agency\BootstrapBackend',

        'common\modules\realty\Bootstrap',
        'common\modules\helpdesk\BootstrapBackend',

        'common\modules\callback\BootstrapBackend',
        'common\modules\reviews\BootstrapBackend',

        'backend\modules\partners\Bootstrap',

        'common\modules\articles\BootstrapBackend',
        'common\modules\pages\BootstrapBackend',

        'backend\modules\merchant\Bootstrap',

        'common\modules\seo\BootstrapBackend',
    ],
    'on beforeRequest' => function ($event) {

        // Настройки по умолчанию для виджета faceviewer
        \Yii::$container->set('nepster\faceviewer\Widget', [
            'faceDefault' => 'no-avatar.png',
            'faceUrl' => Yii::$app->params['siteDomain'] . Yii::$app->getModule('users')->avatarUrl,
            'facePath' => Yii::$app->getModule('users')->avatarPath,
            'faceUrlDefault' => Yii::$app->params['siteDomain'] . '/basic-images',
            'userModel' => 'common\modules\users\models\User',
            'userModelAttributes' => ['username', 'profile' => ['avatar_url', 'name', 'surname']],
        ]);

    },
    'on beforeAction' => function ($event) {

        $relogin = false;
        if (!Yii::$app->user->isGuest) {
            $group = Yii::$app->user->identity->group;
            if (!in_array($group, Yii::$app->getModule('users')->accessGroupsToControlpanel)) {
                if (!in_array($event->action->id, ['login', 'error'])) {
                    $relogin = true;
                }
            }
        } else {
            $relogin = true;
        }

        if ($relogin) {
            Yii::$app->user->logout();
            if (!in_array($event->action->id, ['login', 'error'])) {
                return Yii::$app->controller->redirect(['/users/guest/login']);
            }
        }
    },
    'defaultRoute' => 'admin/default/index',
    'modules' => [
        'admin' => [
            'class' => 'backend\modules\admin\Module'
        ],
        'users' => [
            'class' => 'common\modules\users\Module',
            'controllerNamespace' => 'common\modules\users\controllers\backend',
        ],
        'agency' => [
            'class' => 'common\modules\agency\Module',
            'controllerNamespace' => 'common\modules\agency\controllers\backend',
        ],
        'helpdesk' => [
            'class' => 'common\modules\helpdesk\Module',
            'controllerNamespace' => 'common\modules\helpdesk\controllers\backend',
        ],
        'seo' => [
            'class' => 'common\modules\seo\Module',
            'controllerNamespace' => 'common\modules\seo\controllers\backend',
        ],
        'callback' => [
            'class' => 'common\modules\callback\Module',
            'controllerNamespace' => 'common\modules\callback\controllers\backend',
        ],
        'reviews' => [
            'class' => 'common\modules\reviews\Module',
            'controllerNamespace' => 'common\modules\reviews\controllers\backend',
        ],

        'partners' => [
            'class' => 'backend\modules\partners\Module'
        ],
        'merchant' => [
            'class' => 'backend\modules\merchant\Module'
        ],
        'articles' => [
            'class' => 'common\modules\articles\Module',
            'controllerNamespace' => 'common\modules\articles\controllers\backend',
        ],
        'pages' => [
            'class' => 'common\modules\pages\Module',
            'controllerNamespace' => 'common\modules\pages\controllers\backend',
        ],
        'realty' => [
            'class' => 'common\modules\realty\Module',
            'controllerNamespace' => 'common\modules\realty\controllers\backend',
        ],
        'logs' => [
            'class' => 'backend\modules\logs\Module',
        ],
    ],
    'components' => [
        'navigation' => [
            'class' => '\backend\components\Navigation',
        ],
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@app/views' => '@app/themes/basic',
                    '@app/modules' => '@app/themes/basic/modules',
                ],
                'baseUrl' => '@web/themes/basic',
            ],
        ],
        'request' => [
            'enableCsrfValidation' => true,
            'enableCookieValidation' => true,
            'baseUrl' => ''
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'admin/default/error',
        ],
        'assetManager' => [
            'basePath' => '@webroot/assets',
            'baseUrl' => '@web/assets'
        ],
    ],
    'params' => $params,
];
