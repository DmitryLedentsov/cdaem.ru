<?php

return [
    [
        'class' => \yii\filters\PageCache::class,
        'only' => ['index'],
        'duration' => 300,
        'variations' => Yii::$app->request->queryParams,
        'dependency' => [
            'class' => \yii\caching\DbDependency::class,
            'sql' => "SELECT MAX(update_time) FROM tables_update_time WHERE `table` IN (
                        'agency_adverts',
                        'agency_apartments',
                        'agency_apartment_metro_stations',
                        'agency_apartment_images'
                        )",
        ],
    ],

    [
        'class' => \yii\filters\PageCache::class,
        'only' => ['view'],
        'duration' => 300,
        'enabled' => Yii::$app->request->isGet,
        'variations' => Yii::$app->request->queryParams,
        'dependency' => [
            'class' => 'yii\caching\ChainedDependency',
            'dependencies' => [
                new \yii\caching\ExpressionDependency([
                    'expression' => '\common\modules\agency\models\Advert::find()->select("date_update")
                                ->joinWith("apartment")->where(["advert_id" => Yii::$app->request->get("id")])->scalar()'
                ]),
            ],
        ]
    ],
];
