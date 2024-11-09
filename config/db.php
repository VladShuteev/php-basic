<?php

return [
    'class' => 'yii\db\Connection',
//    'dsn' => 'pgsql:host=aws-0-eu-central-1.pooler.supabase.com;port=6543;dbname=postgres',
//    'username' => 'postgres.rervnpwkbtjelbpvgkqb',
//    'password' => 'Tim95zorgvlad210495',
    'dsn' => getenv('DB_DSN') ?: 'pgsql:host=localhost;dbname=mydb',
    'username' => getenv('DB_USER') ?: 'myuser',
    'password' => getenv('DB_PASSWORD') ?: 'mypassword',
    'charset' => 'utf8',
    'enableLogging' => true,
    'enableProfiling' => true,
];
