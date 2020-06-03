<?php

return [
    'class' => 'yii\swiftmailer\Mailer',
    'viewPath' => '@common/mails',
    'htmlLayout' => '@common/mails/layouts/html',
    'textLayout' => '@common/mails/layouts',
    'useFileTransport' => false,
    'messageConfig' => [
        'from' => ['type.apartment@yandex.ua' => 'Cdaem.ru']
    ],
    'transport' => [
        'class' => 'Swift_SmtpTransport',
        'host' => 'smtp.yandex.ru',
        'username' => 'type.apartment@yandex.ua',
        'password' => '123456789q',
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