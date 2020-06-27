<?php

return [
    'components' => [
        'user' => [
            'identityCookie' => [
                'name' => '_identity',
                'httpOnly' => true,
                'domain' => '.cdaem.com',
            ],
        ],
        'session' => [
            'cookieParams' => ['domain' => '.cdaem.com'],
        ],
    ],
];
