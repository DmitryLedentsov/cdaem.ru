<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'DSFgksdifhiw899734hekfDFGisjdfi9374',
        ],
        'view' => [
            'renderers' => [
                'twig' => [
                    'options' => [
                        'cache' => false,
                    ],
                ],
            ],
        ],
    ],
];

if (YII_ENV_TEST || YII_ENV == 'dev') {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '172.19.0.1']
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '172.19.0.1']
    ];
}

return $config;
