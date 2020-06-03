<?php

return [
    'components' => [
        'user' => [
            'identityCookie' => [
                'name' => '_identity',
                'httpOnly' => true,
                'domain' => '.c.ru',
            ],
        ],
        'session' => [
            'cookieParams' => ['domain' => '.c.ru'],
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=127.0.0.1;dbname=dev.cdaem.ru',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'tablePrefix' => '',
            'enableSchemaCache' => false,
        ],
        'cache' => [
            'class' => 'yii\caching\DummyCache',
        ],
    ],
];
