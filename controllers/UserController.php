<?php

namespace app\controllers;

use app\models\useCases\Auth;
use app\models\useCases\SignUp;
use app\services\JwtTokenService;
use Yii;
use yii\base\InvalidConfigException;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\HttpException;
use yii\web\Response;

class UserController extends Controller
{
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

    /**
     * @return array{status: string, message: string}
     *
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function actionLogin()
    {
        $model = new Auth();
        $model->load(Yii::$app->request->getBodyParams(), '');

        try {
            $user = $model->login();

            $token = JwtTokenService::generate($user);

            Yii::$app->response->cookies->add(new Cookie([
                'name' => 'token',
                'value' => $token,
                'httpOnly' => true,
                'expire' => time() + (60 * 60),  // 1 час
            ]));

            return ['status' => 'success', 'message' => 'Login success'];
        } catch (\Exception $e) {
            throw new HttpException(400, $e->getMessage());
        }
    }

    /**
     * @return array{status: string, message: string, id: int}
     *
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function actionRegister()
    {
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
