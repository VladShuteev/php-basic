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
class User extends ActiveRecord implements IdentityInterface
{
    
    public function rules()
    {
        return [
            [['email'], 'required'],
            ['email', 'email'],
        ];
    }

    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
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
}
