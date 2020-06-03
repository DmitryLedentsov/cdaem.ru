<?php

$pageExistQueryParams = Yii::$app->request->queryParams;
$pageExistQueryParams['page'] = empty($pageExistQueryParams['page']) ? '1' : $pageExistQueryParams['page'];


$filters = [

    /*[
        'class' => 'yii\filters\PageCache',
        'only' => ['help'],
        'duration' => 0,
        'variations' => [Yii::$app->request->queryParams, Yii::$app->request->bodyParams],
        'dependency' => [
            'class' => 'yii\caching\DbDependency',
            'sql' => "SELECT update_time FROM tables_update_time WHERE `table` = 'pages'"
        ]
    ],*/

    [
        'class' => 'yii\filters\PageCache',
        'only' => ['orders'],
        'duration' => 300,
        'variations' => array_merge($pageExistQueryParams, ['user_id' => Yii::$app->user->id]),
        'dependency' => [
            'class' => 'yii\caching\ChainedDependency',
            'dependencies' => [
                new \yii\caching\DbDependency([
                    'sql' => "SELECT COUNT(IF(`process` = 1, 1, NULL)) as `processed`, COUNT(IF(`process` = 0, 1, NULL)) as `awaiting`
                        FROM `partners_services` WHERE `user_id` = :user_id;",
                    'params' => [
                        ':user_id' => Yii::$app->user->id
                    ],
                    'reusable' => true,
                ]),
                new \yii\caching\DbDependency([
                    'sql' => "SELECT MAX(update_time) FROM {{%tables_update_time}} WHERE `table` IN (
                                'partners_advertisement_slider',
                                'users_banned'
                            )",
                    'reusable' => true,
                ]),
                new \yii\caching\DbDependency([
                    'sql' => "SELECT count(*) FROM {{%partners_advertisement_slider}}",
                    'reusable' => true,
                ])
            ],
        ]
    ],

    [
        'class' => 'yii\filters\PageCache',
        'only' => ['top-slider'],
        'duration' => 300,
        'variations' => array_merge($pageExistQueryParams, [
            'user_id' => Yii::$app->user->id,
            'user_type' => \common\modules\users\models\Profile::find()->select('user_type')->where(['user_id' => Yii::$app->user->id])->scalar(),

        ]),
        'dependency' => [
            'class' => 'yii\caching\ChainedDependency',
            'dependencies' => [
                new \yii\caching\DbDependency([
                    'sql' => "SELECT COUNT(IF(`payment` = 1, 1, NULL)) as `paid`,
                        COUNT(IF(`payment` = 0, 1, NULL)) as `awaiting`,
                        COUNT(IF(`visible` = 1, 1, NULL)) as `visible`
                        FROM {{%partners_advertisement_slider}} WHERE `user_id` = :user_id;",
                    'params' => [
                        ':user_id' => Yii::$app->user->id
                    ],
                    'reusable' => true,
                ]),
                new \yii\caching\DbDependency([
                    'sql' => "SELECT MAX(update_time) FROM {{%tables_update_time}} WHERE `table` IN (
                                'partners_advertisement_slider',
                                'users_banned'
                            )",
                    'reusable' => true,
                ]),
                new \yii\caching\DbDependency([
                    'sql' => "SELECT count(*) FROM {{%partners_advertisement_slider}}",
                    'reusable' => true,
                ]),
                new \yii\caching\DbDependency([
                    'sql' => "SELECT MAX(date_update), count(*) FROM {{%partners_apartments}} WHERE `user_id` = :user_id",
                    'params' => [
                        ':user_id' => Yii::$app->user->id,
                    ],
                    'reusable' => true,
                ]),
            ],
        ]
    ],

    [
        'class' => 'yii\filters\PageCache',
        'only' => ['bookmark', 'blacklist'],
        'duration' => 300,
        'variations' => array_merge($pageExistQueryParams, [
            'user_id' => Yii::$app->user->id,
            'user_type' => \common\modules\users\models\Profile::find()->select('user_type')->where(['user_id' => Yii::$app->user->id])->scalar(),

        ]),
        'dependency' => [
            'class' => 'yii\caching\ChainedDependency',
            'dependencies' => [
                new \yii\caching\DbDependency([
                    'sql' => "SELECT MAX(date_update), count(*) FROM {{%partners_apartments}} WHERE user_id = :user_id",
                    'params' => [
                        ':user_id' => Yii::$app->user->id,
                    ],
                    'reusable' => true
                ]),
                new \yii\caching\DbDependency([
                    'sql' => "SELECT MAX(update_time) FROM {{%tables_update_time}} WHERE `table` IN (
                                'partners_advertisement_slider',
                                'users_banned'
                            )",
                    'reusable' => true,
                ]),
                new \yii\caching\DbDependency([
                    'sql' => "SELECT count(*) FROM {{%partners_advertisement_slider}}",
                    'reusable' => true,
                ]),
                new \yii\caching\DbDependency([
                    'sql' => "SELECT COUNT(*), SUM(`record_id`) FROM {{%users_list}} WHERE `user_id` = :user_id AND
                        `type` = :type",
                    'params' => [
                        ':user_id' => Yii::$app->user->id,
                        ':type' => Yii::$app->controller->action->id == 'bookmark' ? 1 : 0,
                    ]
                ]),
            ],
        ]
    ],
];

unset($pageExistQueryParams);

return $filters;