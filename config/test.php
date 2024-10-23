<?php
$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/test_db.php';

/**
 * Application configuration shared by all test types
 */
return [
    'id' => 'basic-tests',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'language' => 'en-US',
    'components' => [
        'db' => $db,
        'queue' => [
            'class' => \yii\queue\redis\Queue::class,
            'redis' => 'redis', // или настройте подключение к Redis здесь
            'channel' => 'queue-test', // используйте отдельный канал для тестов
            'as log' => \yii\queue\LogBehavior::class,
        ],
        'redis' => [
            'class' => \yii\redis\Connection::class,
            'hostname' => 'localhost',
            'port' => 6379,
            'database' => 1, // используйте отдельную базу данных Redis для тестов
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
            'messageClass' => 'yii\symfonymailer\Message'
        ],
        'assetManager' => [
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
//        'user' => [
//            'identityClass' => 'app\models\entities\User',
//        ],
//        'request' => [
//            'cookieValidationKey' => 'test',
//            'enableCsrfValidation' => false,
//            // but if you absolutely need it set cookie domain to localhost
//            /*
//            'csrfCookie' => [
//                'domain' => 'localhost',
//            ],
//            */
//        ],
    ],
    'params' => $params,
];
