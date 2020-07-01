<?php

return [
    'components' => [
        'user' => [
            'identityCookie' => [
                'name' => '_identity',
                'httpOnly' => true,
                'domain' => '.cdaem.ru',
            ],
        ],
        'session' => [
            'cookieParams' => ['domain' => '.cdaem.ru'],
        ],
    ],
];
