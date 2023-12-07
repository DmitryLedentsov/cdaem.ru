<?php

return [
    'components' => [
        'user' => [
            'identityCookie' => [
                'name' => '_identity',
                'httpOnly' => true,
                'domain' => '.cdaem.loc',
            ],
        ],
        'session' => [
            'cookieParams' => ['domain' => '.cdaem.loc'],
        ],
        'db' => [
            'class' => \yii\db\Connection::class,
            'dsn' => 'mysql:host=db;dbname=cdaem.ru',
            'username' => 'root',
            'password' => 'cdaemru',
            'charset' => 'utf8mb4',
            'tablePrefix' => '',
            'enableSchemaCache' => false,
        ],
        'cache' => [
            'class' => \yii\caching\DummyCache::class,
        ],
        'robokassa' => [
            // 'mrchLogin' => 'cdaemru',
            'mrchLogin' => 'devcdaem',
            'mrchPassword1' => '*******',
            'mrchPassword2' => '*******',
        ],
    ],
    'params' => [
        'siteDomain' => 'http://cdaem.loc:84',
        'siteSubDomain' => 'http://<city>.cdaem.loc:84',
    ]
];
