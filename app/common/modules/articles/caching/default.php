<?php

$pageExistQueryParams = Yii::$app->request->queryParams;
$pageExistQueryParams['page'] = empty($pageExistQueryParams['page']) ? '1' : $pageExistQueryParams['page'];

$filters = [

    [
        'class' => \yii\filters\PageCache::class,
        'only' => ['index', 'view'],
        'duration' => 0,
        'variations' => $pageExistQueryParams,
        'dependency' => [
            'class' => \yii\caching\DbDependency::class,
            'sql' => "SELECT `update_time` FROM {{%tables_update_time}} WHERE `table` = 'articles'",
        ],
    ],

];

unset($pageExistQueryParams);

return $filters;
