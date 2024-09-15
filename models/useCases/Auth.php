<?php

namespace app\models\useCases;

use yii\base\Exception;
use yii\base\Model;
use \app\models\entities\User;

class Auth extends Model {
    public $email;
    public $password;

    public function rules() {
        return [
            [['email', 'password'], 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['password', 'string', 'min' => 6],
        ];
    }

    public function login() {
        $user = User::findOne(['email' => $this->email]);

        if (!$user) {
            throw new Exception('User not found');
        }

        if (!$user->validatePassword($this->password)) {
            throw new Exception('Wrong password');
        }

        return $user;
    }
}
