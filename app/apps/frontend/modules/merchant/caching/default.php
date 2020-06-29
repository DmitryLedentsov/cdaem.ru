<?php

$pageExistQueryParams = Yii::$app->request->queryParams;
$pageExistQueryParams['page'] = empty($pageExistQueryParams['page']) ? '1' : $pageExistQueryParams['page'];


$filters = [

    [
        'class' => 'yii\filters\PageCache',
        'only' => ['index'],
        'duration' => 300,
        'variations' => array_merge($pageExistQueryParams, [
            'user_id' => Yii::$app->user->id,
            'user_type' => \common\modules\users\models\Profile::find()->select('user_type')->where(['user_id' => Yii::$app->user->id])->scalar(),

        ]),
        'dependency' => [
            'class' => 'yii\caching\ChainedDependency',
            'dependencies' => [
                new \yii\caching\DbDependency([
                    'sql' => "SELECT COUNT(*) FROM {{%merchant_payments}} WHERE `user_id` = :user_id",
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
                    'reusable' => true
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