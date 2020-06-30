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
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=db;dbname=cdaemru',
            'username' => 'root',
            'password' => 'cdaemru',
            'charset' => 'utf8',
            'tablePrefix' => '',
            'enableSchemaCache' => false,
        ],
        'cache' => [
            'class' => 'yii\caching\DummyCache',
        ],
    ],
];
