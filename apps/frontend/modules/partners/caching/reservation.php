<?php

// ��� ��� empty(page) ��� � ���� ������ ��������, � �������� ��� ������ ���� ����� � ��� ��
$pageExistQueryParams = Yii::$app->request->queryParams;
$pageExistQueryParams['page'] = empty($pageExistQueryParams['page']) ? '1' : $pageExistQueryParams['page'];

// ����� ��������� �� ����������� ���� � �������, �� ����� ������� ��� �����
$cityIdQuery = '';
if (Yii::$app->request->cityId) $cityIdQuery = ' city_id = ' . Yii::$app->request->cityId;

$whereCityIdQuery = $cityIdQuery ? ' WHERE' . $cityIdQuery : '';

$filters = [

    [
        'class' => 'yii\filters\PageCache',
        'only' => ['reservations'],
        'duration' => 300,
        'enabled' => \common\modules\users\models\Profile::find()->select('user_type')
            ->where(['user_id' => Yii::$app->user->id])->scalar() != \common\modules\users\models\Profile::WANT_RENT,
        'variations' => array_merge($pageExistQueryParams, [
            'user_id' => Yii::$app->user->id,
        ]),
        'dependency' => [
            'class' => 'yii\caching\ChainedDependency',
            'dependencies' => [
                new \yii\caching\DbDependency([
                    'sql' => "SELECT MAX(date_update), COUNT(*) FROM {{%partners_advert_reservations}} WHERE `landlord_id` = :user_id",
                    'params' => [
                        ':user_id' => Yii::$app->user->id,
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
                new \yii\caching\DbDependency([
                    'sql' => "SELECT MAX(date_update), COUNT(*) FROM {{%partners_apartments}} WHERE `user_id` = :user_id",
                    'params' => [
                        ':user_id' => Yii::$app->user->id,
                    ]
                ]),
            ],
        ]
    ],

    [
        'class' => 'yii\filters\PageCache',
        'only' => ['reservations-want-rent'],
        'duration' => 300,
        'variations' => array_merge($pageExistQueryParams, [
            'user_id' => Yii::$app->user->id,
        ]),
        'dependency' => [
            'class' => 'yii\caching\ChainedDependency',
            'dependencies' => [
                new \yii\caching\DbDependency([
                    'sql' => "SELECT MAX(date_update), COUNT(*) FROM {{%partners_advert_reservations}} WHERE `user_id` = :user_id",
                    'params' => [
                        ':user_id' => Yii::$app->user->id,
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
                new \yii\caching\DbDependency([
                    'sql' => "SELECT MAX(date_update), COUNT(*) FROM {{%partners_apartments}} WHERE `user_id` = :user_id",
                    'params' => [
                        ':user_id' => Yii::$app->user->id,
                    ]
                ]),
            ],
        ]
    ],

    [
        'class' => 'yii\filters\PageCache',
        'only' => ['total-bid'],
        'duration' => 300,
        'enabled' => \common\modules\users\models\Profile::find()->select('user_type')
                ->where(['user_id' => Yii::$app->user->id])->scalar() != \common\modules\users\models\Profile::WANT_RENT,
        'variations' => array_merge($pageExistQueryParams, [
            'user_id' => Yii::$app->user->id,
        ]),
        'dependency' => [
            'class' => 'yii\caching\ChainedDependency',
            'dependencies' => [
                new \yii\caching\DbDependency([
                    'sql' => "SELECT MAX(date_update), COUNT(*) FROM {{%partners_reservations}} " . $whereCityIdQuery,
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
                    'sql' => "SELECT MAX(date_update), COUNT(*) FROM {{%partners_apartments}} WHERE `user_id` = :user_id",
                    'params' => [
                        ':user_id' => Yii::$app->user->id,
                    ]
                ]),
            ],
        ]
    ],

    [
        'class' => 'yii\filters\PageCache',
        'only' => ['total-bid-want-rent'],
        'duration' => 300,
        'variations' => array_merge($pageExistQueryParams, [
            'user_id' => Yii::$app->user->id,
        ]),
        'dependency' => [
            'class' => 'yii\caching\ChainedDependency',
            'dependencies' => [
                new \yii\caching\DbDependency([
                    'sql' => "SELECT MAX(date_update), COUNT(*) FROM {{%partners_reservations}} WHERE user_id = :user_id",
                    'params' => [':user_id' => Yii::$app->user->id]
                ]),
                new \yii\caching\DbDependency([
                    'sql' => "SELECT MAX(update_time) FROM {{%tables_update_time}} WHERE `table` IN (
                                'partners_advertisement_slider',
                                'users_banned'
                            )"
                ]),
                new \yii\caching\DbDependency([
                    'sql' => "SELECT count(*) FROM {{%partners_advertisement_slider}}",
                    'reusable' => true,
                ]),
            ],
        ]
    ],

];

unset($pageExistQueryParams);
unset($cityIdQuery);
unset($whereCityIdQuery);

return $filters;