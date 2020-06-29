<?php

return [

    [
        'class' => 'yii\filters\HttpCache',
        'only' => ['index'],
        'lastModified' => function ($action, $params) {
            $q = new \yii\db\Query();
            return $q->select('update_time')->from('tables_update_time')->where(['table' => 'pages'])->scalar();
        }
    ],

    [
        'class' => 'yii\filters\PageCache',
        'only' => ['index'],
        'duration' => 0,
        'variations' => Yii::$app->request->queryParams,
        'dependency' => [
            'class' => 'yii\caching\DbDependency',
            'sql' => "SELECT `update_time` FROM {{%tables_update_time}} WHERE `table` = 'pages'",
        ],
    ],
];