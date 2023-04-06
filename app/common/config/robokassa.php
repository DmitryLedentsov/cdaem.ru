<?php

return [
    'class' => \nepster\robokassa\Api::class,
    // 'mrchLogin' => 'cdaemru',
    'mrchLogin' => 'devcdaem',
    'mrchPassword1' => '********',
    'mrchPassword2' => '********',
    'resultUrl' => ['/merchant/robokassa/result'],
    'successUrl' => ['/merchant/robokassa/success'],
    'failureUrl' => ['/merchant/robokassa/failure']
];
