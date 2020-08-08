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
                        'users_banned',
                        'partners_adverts',
                        'agency_adverts',
                        'partners_apartments',
                        'agency_apartments',
                        'partners_apartment_metro_stations',
                        'agency_apartment_metro_stations',
                        'partners_apartment_images',
                        'agency_apartment_images',
                        'seo_text'
                        )",
        ],
    ],
];
