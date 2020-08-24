<?php

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        common\modules\admin\Bootstrap::class,
        common\modules\users\Bootstrap::class,
        common\modules\agency\BootstrapBackend::class,

        common\modules\realty\BootstrapBackend::class,
        common\modules\helpdesk\BootstrapBackend::class,

        common\modules\callback\BootstrapBackend::class,
        common\modules\reviews\BootstrapBackend::class,

        common\modules\partners\BootstrapBackend::class,

        common\modules\articles\BootstrapBackend::class,
        common\modules\pages\BootstrapBackend::class,

        common\modules\merchant\BootstrapBackend::class,

        common\modules\seo\BootstrapBackend::class,
    ],
    'on beforeRequest' => function ($event) {
        // Настройки по умолчанию для виджета faceviewer
        \Yii::$container->set(\nepster\faceviewer\Widget::class, [
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
            'class' => \common\modules\admin\Module::class,
        ],
        'users' => [
            'class' => \common\modules\users\Module::class,
            'controllerNamespace' => 'common\modules\users\controllers\backend',
        ],
        'agency' => [
            'class' => \common\modules\agency\Module::class,
            'controllerNamespace' => 'common\modules\agency\controllers\backend',
        ],
        'helpdesk' => [
            'class' => \common\modules\helpdesk\Module::class,
            'controllerNamespace' => 'common\modules\helpdesk\controllers\backend',
        ],
        'seo' => [
            'class' => \common\modules\seo\Module::class,
            'controllerNamespace' => 'common\modules\seo\controllers\backend',
        ],
        'callback' => [
            'class' => \common\modules\callback\Module::class,
            'controllerNamespace' => 'common\modules\callback\controllers\backend',
        ],
        'reviews' => [
            'class' => \common\modules\reviews\Module::class,
            'controllerNamespace' => 'common\modules\reviews\controllers\backend',
        ],
        'partners' => [
            'class' => \common\modules\partners\Module::class,
            'controllerNamespace' => 'common\modules\partners\controllers\backend',
        ],
        'merchant' => [
            'class' => \common\modules\merchant\Module::class,
            'controllerNamespace' => 'common\modules\merchant\controllers\backend',
        ],
        'articles' => [
            'class' => \common\modules\articles\Module::class,
            'controllerNamespace' => 'common\modules\articles\controllers\backend',
        ],
        'pages' => [
            'class' => \common\modules\pages\Module::class,
            'controllerNamespace' => 'common\modules\pages\controllers\backend',
        ],
        'realty' => [
            'class' => \common\modules\realty\Module::class,
            'controllerNamespace' => 'common\modules\realty\controllers\backend',
        ],
    ],
    'components' => [
        'navigation' => [
            'class' => \backend\components\Navigation::class,
        ],
        'request' => [
            'enableCsrfValidation' => true,
            'enableCookieValidation' => true,
            'baseUrl' => '',
            'as cityModelBehavior' => \common\behaviors\CityModelBehavior::class
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
        'errorHandler' => [
            'errorAction' => 'admin/default/error',
        ],
        'assetManager' => [
            'basePath' => '@webroot/assets',
            'baseUrl' => '@web/assets'
        ],
    ],
];
