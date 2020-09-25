<?php

return [
    'class' => \yii\swiftmailer\Mailer::class,
    'viewPath' => '@common/mails',
    'htmlLayout' => '@common/mails/layouts/html',
    'textLayout' => '@common/mails/layouts',
    'useFileTransport' => false,
    'messageConfig' => [
        'from' => ['cdaem.ru@yandex.ru' => 'Cdaem.ru']
    ],
    'transport' => [
        'class' => 'Swift_SmtpTransport',
        'host' => 'smtp.yandex.ru',
        'username' => 'cdaem.ru@yandex.ru',
        'password' => 'fd_lkwre42',
        'port' => '465',
        'encryption' => 'ssl',
    ],
    /*'transport' => [
        'class' => 'Swift_SmtpTransport',
        'host' => 'smtp.gmail.com',
        'username' => 'myaccount@gmail.com',
        'password' => '',
        'port' => '587',
        'encryption' => 'tls',
    ],*/
];
