<?php

namespace app\models\entities;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * @property int $id
 * @property string $email
 * @property string $password_hash
 */
class User extends ActiveRecord implements IdentityInterface {
    public function rules()
    {
        return [
            [['email'], 'required'],
            ['email', 'email'],
        ];
    }

    public static function findIdentity($id)
    {
        return User::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;  // Для JWT это может быть не нужно
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return null;
    }

    public function validateAuthKey($authKey)
    {
        return true;
    }

    public static function validateJwtToken($token) {
        $secretKey = Yii::$app->params['jwtSecretKey'];

        return JWT::decode($token, new Key($secretKey, 'HS256'));
    }

    public function generateJwtToken() {
        $secretKey = Yii::$app->params['jwtSecretKey'];
        $payload = [
            'iss' => 'mini_manychat',
            'aud' => 'web',
            'iat' => time(),
            'exp' => time() + (60 * 60),  // 1 час
            'data' => [
                'userId' => $this->id,
                'email' => $this->email,
            ],
        ];

        return JWT::encode($payload, $secretKey, 'HS256');
    }


    public function setPassword($password) {
        $this->password_hash = Yii::$app->getSecurity()->generatePasswordHash($password);
    }

    public function validatePassword($password) {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password_hash);
    }
}