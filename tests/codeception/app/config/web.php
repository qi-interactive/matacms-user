<?php

return [
    'id'        => 'yii2-user-test',
    'basePath'  => dirname(__DIR__),
    'bootstrap' => [
        'mata\user\Bootstrap'
    ],
    'extensions' => require(VENDOR_DIR . '/yiisoft/extensions.php'),
    'aliases' => [
        '@mata/user' => realpath(__DIR__. '/../../../../'),
        '@vendor'        => VENDOR_DIR,
        '@bower'         => VENDOR_DIR . '/bower',
    ],
    'modules' => [
        'user' => [
            'class' => 'mata\user\Module',
            'admins' => ['user'],
            'mailer' => [
                'class' => 'app\components\MailerMock',
            ],
        ]
    ],
    'components' => [
        'assetManager' => [
            'basePath' => '@tests/codeception/app/assets'
        ],
        'log'   => null,
        'cache' => null,
        'request' => [
            'enableCsrfValidation'   => false,
            'enableCookieValidation' => false
        ],
        'db' => require __DIR__ . '/db.php',
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true
        ],
    ],
];
