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
            'charset' => 'utf8',
            'tablePrefix' => '',
            'enableSchemaCache' => false,
        ],
        'cache' => [
            'class' => \yii\caching\DummyCache::class,
        ],
    ],
    'params' => [
        'siteDomain' => 'http://cdaem.loc',
        'siteSubDomain' => 'http://<city>.cdaem.loc',
    ]
];
