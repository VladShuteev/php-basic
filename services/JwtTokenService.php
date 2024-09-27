<?php

namespace app\services;

use app\models\entities\User;
use Yii;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtTokenService
{
    public static function validate($token)
    {
        $secretKey = Yii::$app->params['jwtSecretKey'];

        return JWT::decode($token, new Key($secretKey, 'HS256'));
    }

    public static function generate(User $user)
    {
        $secretKey = Yii::$app->params['jwtSecretKey'];
        $payload = [
            'iss' => 'mini_manychat',
            'aud' => 'web',
            'iat' => time(),
            'exp' => time() + (60 * 60),  // 1 час
            'data' => [
                'userId' => $user->id,
            ],
        ];

        return JWT::encode($payload, $secretKey, 'HS256');
    }
}
