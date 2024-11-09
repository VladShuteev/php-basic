<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'queue'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
    'components' => [
        'redis' => function () {
            $redisUrl = getenv('REDIS_URL');
            if (!$redisUrl) {
                throw new \Exception('REDIS_URL environment variable is not set');
            }

            $parts = parse_url($redisUrl);

            return [
                'class' => \yii\redis\Connection::class,
                'hostname' => $parts['host'],
                'port' => $parts['port'],
                'password' => isset($parts['pass']) ? $parts['pass'] : null,
                'database' => 0,
            ];
        },
        'queue' => [
            'class' => \yii\queue\redis\Queue::class,
            'redis' => 'redis',
            'channel' => 'queue',
            'as log' => \yii\queue\LogBehavior::class,
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
    ],
    'params' => $params,
    'controllerMap' => [
        'queue' => [
            'class' => \yii\queue\redis\Command::class,
            'queue' => 'queue',
        ],
    ],
];

//if (YII_ENV_DEV) {
//    // configuration adjustments for 'dev' environment
//    $config['bootstrap'][] = 'gii';
//    $config['modules']['gii'] = [
//        'class' => 'yii\gii\Module',
//    ];
//    // configuration adjustments for 'dev' environment
//    // requires version `2.1.21` of yii2-debug module
//    $config['bootstrap'][] = 'debug';
//    $config['modules']['debug'] = [
//        'class' => 'yii\debug\Module',
//    ];
//}

return $config;
