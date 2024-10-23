<?php

namespace app\models\useCases;

use app\services\PasswordHashService;
use yii\base\Exception;
use yii\base\Model;
use \app\models\entities\User;
use yii\web\NotFoundHttpException;

class Auth extends Model
{
    public string $email;
    public string $password;

    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['password', 'string', 'min' => 6],
        ];
    }

    public function login(): User
    {
        $user = User::findOne(['email' => $this->email]);

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        if (!PasswordHashService::validate($this->password, $user->password_hash)) {
            throw new Exception('Wrong password');
        }

        return $user;
    }
}
