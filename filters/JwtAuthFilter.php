<?php

namespace app\filters;

use app\models\entities\User;
use Yii;
use yii\filters\auth\AuthMethod;

class JwtAuthFilter extends AuthMethod {

    public function authenticate($user, $request, $response)
    {
        $jwtToken = Yii::$app->request->cookies->getValue('token');

        if ($jwtToken) {
            try {
                $decoded = User::validateJwtToken($jwtToken);

                $userData = (array) $decoded->data;

                $identity = User::findOne($userData['userId']);

                if ($identity) {
                    $user->login($identity);
                    return $identity;
                }

            } catch (\Exception $e) {
                Yii::$app->response->statusCode = 401;
                return null;
            }
        }

        Yii::$app->response->statusCode = 401;
        return null;
    }

//    Сейчас выдает 500, а должен 401
    public function challenge($response) {
        $response->setStatusCode(401);
        $response->data = ['status' => 'error', 'message' => 'Unauthorized'];
    }
}
