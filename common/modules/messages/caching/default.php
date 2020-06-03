<?php

return [
    [
        'class' => 'yii\filters\PageCache',
        'only' => ['index'],
        'duration' => 300,
        'variations' => [
            'user_id' => Yii::$app->user->id,
            'user_type' => \common\modules\users\models\Profile::find()->select('user_type')->where(['user_id' => Yii::$app->user->id])->scalar(),
        ],
        'dependency' => [
            'class' => 'yii\caching\ChainedDependency',
            'dependencies' => [
                new \yii\caching\DbDependency([
                    'sql' => "SELECT MAX(date_update) FROM {{%partners_apartments}} WHERE `user_id` = :user_id",
                    'params' => [
                        ':user_id' => Yii::$app->user->id,
                    ]
                ]),
                new \yii\caching\DbDependency([
                    'sql' => "SELECT MAX(update_time) FROM {{%tables_update_time}} WHERE `table` IN (
                                'partners_advertisement_slider',
                                'users_banned'
                            )"
                ]),
                new \yii\caching\DbDependency([
                    'sql' => "SELECT MAX(id) FROM {{%user_messages_mailbox}} WHERE `user_id` = :user_id
                            AND `deleted` = :not",
                    'params' => [
                        ':user_id' => Yii::$app->user->id,
                        ':not' => 0
                    ]
                ]),
            ],
        ]
    ],
];