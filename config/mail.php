<?php

return [
    'default' => env('MAIL_MAILER', 'smtp'),

    'mailers' => [
       'smtp' => [
    'transport' => 'smtp',
    'host' => env('MAIL_HOST', 'smtp.titan.email'),
    'port' => env('MAIL_PORT', 465),
    'encryption' => env('MAIL_ENCRYPTION', 'ssl'),
    'username' => env('MAIL_USERNAME'),
    'password' => env('MAIL_PASSWORD'),
    'timeout' => 30,
    'local_domain' => env('MAIL_EHLO_DOMAIN'),
],
    ],

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'info@skillychat.com.ng'),
        'name' => env('MAIL_FROM_NAME', 'M-right Digital Receipt System'),
    ],
];