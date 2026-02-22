<?php

$params = array_merge(
        require __DIR__ . '/../../common/config/params.php',
        require __DIR__ . '/../../common/config/params-local.php',
        require __DIR__ . '/params.php',
        require __DIR__ . '/params-local.php'
);

use \yii\web\Request;

$baseUrl = str_replace('/frontend/web', '', (new Request)->getBaseUrl()); // you set override the frontend/web with blank space and it will return the baseUrl.
return [
    'timeZone' => "Asia/Kuala_Lumpur",
    'id' => 'app-frontend',
    'name' => 'Digital Management System',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'layout' => 'mainLayout',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'baseUrl' => $baseUrl,
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'baseUrl' => $baseUrl,
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'mail.opitsolutions.com.my',
                'port' => '465',
                'encryption' => 'ssl',
                'username' => 'tkdms@opitsolutions.com.my',
                'password' => 'tFNRi=aVdX!',
            ],
        ],
    ],
    'params' => $params,
];
