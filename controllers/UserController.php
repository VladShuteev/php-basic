<?php

namespace app\controllers;

use app\models\useCases\Auth;
use app\models\useCases\SignUp;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\HttpException;
use yii\web\Response;

class UserController extends Controller {
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'login' => ['POST'],
                    'register' => ['POST'],
                ],
            ],
        ];
    }

    public function actionLogin() {
        $model = new Auth();
        $model->load(Yii::$app->request->getBodyParams(), '');

        try {
            $user = $model->login();

            $token = $user->generateJwtToken();

            Yii::$app->response->cookies->add(new Cookie([
                'name' => 'token',
                'value' => $token,
                'httpOnly' => true,
//                'sameSite' => 'None',
                'expire' => time() + (60 * 60),  // 1 час
            ]));

            return ['status' => 'success', 'message' => 'Login success'];
        } catch (\Exception $e) {
            throw new HttpException(400, $e->getMessage());
        }
    }

    public function actionRegister() {
        $model = new SignUp();
        $model->load(Yii::$app->request->getBodyParams(), '');

        try {
            $user = $model->register();

            return ['status' => 'success', 'message' => 'Register success', 'id' => $user->id];
        } catch (\Exception $e) {
            throw new HttpException(400, $e->getMessage());
        }
    }
}