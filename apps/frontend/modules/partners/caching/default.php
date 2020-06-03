<?php

// ��� ��� empty(page) ��� � ���� ������ ��������, � �������� ��� ������ ���� ����� � ��� ��
$pageExistQueryParams = Yii::$app->request->queryParams;
$pageExistQueryParams['page'] = empty($pageExistQueryParams['page']) ? '1' : $pageExistQueryParams['page'];


$filters = [
    [
        'class' => 'yii\filters\PageCache',
        'only' => ['region'],
        'duration' => 300,
        'variations' => $pageExistQueryParams,
        'dependency' => [
            'class' => 'yii\caching\DbDependency',
            'sql' => "SELECT MAX(update_time) FROM tables_update_time WHERE `table` IN (
                        'users_banned',
                        'partners_adverts',
                        'partners_apartments',
                        'partners_advertisement_slider',
                        'partners_apartment_metro_stations',
                        'partners_apartment_images'
                        )",
        ],
    ],

    /*
     * ���������� ��-�� ���� ��� ��� ���� ������������ ����� (navbar)
     [
        'class' => 'yii\filters\HttpCache',
        'only' => ['index'],
        'lastModified' => function ($action, $params) {
            $q = new \yii\db\Query();
            return $q->select('update_time')->from('tables_update_time')->where(['table' => ['partners_apartments', 'partners_advertisement_slider', 'users_banned']])->scalar();
        }
    ]*/

    [
        'class' => 'yii\filters\PageCache',
        'only' => ['index'],
        'duration' => 300,
        'dependency' => [
            'class' => 'yii\caching\DbDependency',
            'sql' => "SELECT update_time FROM {{%tables_update_time}} WHERE `table` IN ('partners_apartments', 'partners_advertisement_slider', 'users_banned')"
        ]
    ],

    [
        'class' => 'yii\filters\PageCache',
        'only' => ['view'],
        'duration' => 300,
        'variations' => Yii::$app->request->queryParams,
        'dependency' => [
            'class' => 'yii\caching\ChainedDependency',
            'dependencies' => [
                new \yii\caching\ExpressionDependency([
                    'expression' => '\frontend\modules\partners\models\Advert::find()->select("date_update")
                                ->joinWith("apartment")->where(["advert_id" => Yii::$app->request->get("id")])->scalar()'
                ]),
                new \yii\caching\DbDependency([
                    'sql' => "SELECT MAX(update_time) FROM {{%tables_update_time}} WHERE `table` IN (
                                'partners_advertisement_slider',
                                'partners_advertisement',
                                'agency_advertisement',
                                'users_banned'
                            )"
                ]),
                new \yii\caching\DbDependency([
                    'sql' => "SELECT count(*) FROM {{%partners_advertisement_slider}}",
                    'reusable' => true,
                ]),
                new \yii\caching\DbDependency([
                    'sql' => "SELECT COUNT(*) FROM {{%reviews}} WHERE `apartment_id` =
                                (SELECT `apartment_id` FROM {{%partners_adverts}} WHERE `advert_id` = :advert_id)
                                AND `moderation` = 1 AND `visible` = 1",
                    'params' => [
                        ':advert_id' => Yii::$app->request->get('id'),
                    ]
                ]),
            ],
        ]
    ],

    [
        'class' => 'yii\filters\PageCache',
        'only' => ['others'],
        'duration' => 300,
        'variations' => Yii::$app->request->queryParams,
        'dependency' => [
            'class' => 'yii\caching\ChainedDependency',
            'dependencies' => [
                new \yii\caching\DbDependency([
                    'sql' => "SELECT MAX(date_update), count(*) FROM {{%partners_apartments}} WHERE `user_id` =
                            (SELECT `user_id` FROM {{%partners_adverts}} LEFT JOIN {{%partners_apartments}} USING (apartment_id)
                             WHERE advert_id = :advert_id)",
                    'params' => [
                        ':advert_id' => Yii::$app->request->get('id'),
                    ]
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
            ],
        ]
    ],

    [
        'class' => 'yii\filters\PageCache',
        'only' => ['apartments'],
        'duration' => 300,
        'variations' => array_merge($pageExistQueryParams, ['user_id' => Yii::$app->user->id]),
        'dependency' => [
            'class' => 'yii\caching\ChainedDependency',
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
        'class' => 'yii\filters\PageCache',
        'only' => ['create'],
        'enabled' => Yii::$app->request->isGet,
        'duration' => 300,
        'dependency' => [
            'class' => 'yii\caching\ChainedDependency',
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
        'class' => 'yii\filters\PageCache',
        'only' => ['update'],
        'enabled' => Yii::$app->request->isGet AND \common\modules\partners\models\Apartment::find()
                ->where(['user_id' => Yii::$app->user->id, 'apartment_id' => Yii::$app->request->get('id')])->exists(),
        'duration' => 300,
        'variations' => [
            'id' => Yii::$app->request->get('id'),
        ],
        'dependency' => [
            'class' => 'yii\caching\ChainedDependency',
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