<?php

namespace app\services;

use Yii;

class PasswordHashService
{
    public static function generate($password)
    {
        return Yii::$app->getSecurity()->generatePasswordHash($password);
    }

    public static function validate($password, $hashedPassword)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $hashedPassword);
    }
}
