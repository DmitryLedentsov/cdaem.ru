<?php

$pageExistQueryParams = Yii::$app->request->queryParams;
$pageExistQueryParams['page'] = empty($pageExistQueryParams['page']) ? '1' : $pageExistQueryParams['page'];


$filters = [

    [
        'class' => \yii\filters\PageCache::class,
        'only' => ['apartments'],
        'duration' => 300,
        'variations' => array_merge($pageExistQueryParams, ['user_id' => Yii::$app->user->id]),
        'dependency' => [
            'class' => \yii\caching\ChainedDependency::class,
            'dependencies' => [
                new \yii\caching\DbDependency([
                    'sql' => "SELECT MAX(date_update), count(*) FROM {{%partners_apartments}} WHERE `user_id` = :user_id",
                    'params' => [
                        ':user_id' => Yii::$app->user->id,
                    ],
                    'reusable' => true,
                ]),
                new \yii\caching\DbDependency([
                    'sql' => "SELECT MAX(update_time) FROM {{%tables_update_time}} WHERE `table` IN (
                                'partners_advertisement_slider',
                                'users_banned'
                            )",
                    'reusable' => true
                ]),
                new \yii\caching\DbDependency([
                    'sql' => "SELECT count(*) FROM {{%partners_advertisement_slider}}",
                    'reusable' => true
                ])
            ],
        ]
    ],

    [
        'class' => \yii\filters\PageCache::class,
        'only' => ['create'],
        'enabled' => Yii::$app->request->isGet,
        'duration' => 300,
        'dependency' => [
            'class' => \yii\caching\ChainedDependency::class,
            'dependencies' => [
                new \yii\caching\DbDependency([
                    'sql' => "SELECT MAX(update_time) FROM {{%tables_update_time}} WHERE `table` IN (
                                'partners_advertisement_slider',
                                'users_banned'
                            )",
                    'reusable' => true
                ]),
                new \yii\caching\DbDependency([
                    'sql' => "SELECT count(*) FROM {{%partners_advertisement_slider}}",
                    'reusable' => true
                ])
            ]
        ]
    ],

    [
        'class' => \yii\filters\PageCache::class,
        'only' => ['update'],
        'enabled' => Yii::$app->request->isGet and \common\modules\partners\models\Apartment::find()
                ->where(['user_id' => Yii::$app->user->id, 'apartment_id' => Yii::$app->request->get('id')])->exists(),
        'duration' => 300,
        'variations' => [
            'id' => Yii::$app->request->get('id'),
        ],
        'dependency' => [
            'class' => \yii\caching\ChainedDependency::class,
            'dependencies' => [
                new \yii\caching\DbDependency([
                    'sql' => "SELECT date_update FROM {{%partners_apartments}} WHERE `apartment_id` = :ap_id",
                    'params' => [
                        ':ap_id' => Yii::$app->request->get('id'),
                    ]
                ]),
                new \yii\caching\DbDependency([
                    'sql' => "SELECT MAX(update_time) FROM {{%tables_update_time}} WHERE `table` IN (
                                'partners_advertisement_slider',
                                'users_banned'
                            )"
                ]),
                new \yii\caching\DbDependency([
                    'sql' => "SELECT count(*) FROM {{%partners_advertisement_slider}}",
                    'reusable' => true
                ])
            ],
        ]
    ],
];

unset($pageExistQueryParams);

return $filters;
