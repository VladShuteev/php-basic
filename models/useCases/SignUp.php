<?php

namespace app\models\useCases;

use app\models\entities\Account;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use app\models\entities\User;

class SignUp extends Model {
    public $email;
    public $password;

    public function rules() {
        return [
            ['email', 'trim'],
            [['email', 'password'], 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => \app\models\entities\User::class, 'message' => 'This email address is already in use.'],
            ['email', 'string', 'max' => 255],
            ['password', 'string', 'min' => 6],
        ];
    }

    public function register() {
        if (!$this->validate()) {
            throw new Exception(json_encode($this->errors));
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $user = new User();
            $user->email = $this->email;
            $user->setPassword($this->password);

            if (!$user->save()) {
                throw new Exception($user->getErrors());
            };

            $account = new Account();
            $account->user_id = $user->id;

            if (!$account->save()) {
                throw new Exception($user->getErrors());
            }

            $transaction->commit();
            return $user;
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}
