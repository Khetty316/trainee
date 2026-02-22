<?php

//$params = array_merge(
//        require __DIR__ . '/../../common/config/params.php',
//        require __DIR__ . '/../../common/config/params-local.php',
//        require __DIR__ . '/params.php',
//        require __DIR__ . '/params-local.php'
//);
//
//return [
//    'id' => 'app-console',
//    'basePath' => dirname(__DIR__),
//    'bootstrap' => ['log'],
//    'controllerNamespace' => 'console\controllers',
//    'aliases' => [
//        '@bower' => '@vendor/bower-asset',
//        '@npm' => '@vendor/npm-asset',
//    ],
//    'controllerMap' => [
//        'fixture' => [
//            'class' => 'yii\console\controllers\FixtureController',
//            'namespace' => 'common\fixtures',
//        ],
//    ],
//    'components' => [
//        'log' => [
//            'targets' => [
//                [
//                    'class' => 'yii\log\FileTarget',
//                    'levels' => ['error', 'warning'],
//                ],
//            ],
//        ],
//        'authManager' => [
//            'class' => 'yii\rbac\PhpManager',
//            'class' => 'yii\rbac\DbManager',
//        ],
//    ],
//    'params' => $params,
//];

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

$config = [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\fixtures',
        ],
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\PhpManager',
            'class' => 'yii\rbac\DbManager',
        ],
    ],
    'params' => $params,
];

// Only add gii in development environment
if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;