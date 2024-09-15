<?php

namespace app\controllers;

use Yii;
use app\models\entities\Account;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\Controller;
use app\filters\JwtAuthFilter;
use yii\web\HttpException;
use yii\web\Response;

class AccountController extends Controller {
    public function behaviors() {
        return [
            'authenticator' => [
                'class' => JwtAuthFilter::class,
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['POST'],
                    'list' => ['GET']
                ],
            ],
        ];
    }

    public function actionList() {
        $userId = Yii::$app->user->id;
        return Account::find()->select(['id', 'name', 'avatar'])->where(['user_id' => $userId])->all();
    }

    public function actionCreate() {
        $model = new Account();
        $model->user_id = Yii::$app->user->id;
        $model->name = Yii::$app->request->post('name');
        $model->avatar = Yii::$app->request->post('avatar');

        if ($model->validate()) {
            $model->save();
            return ['status' => 'success', 'message' => 'Account created', 'account' => $model->getAttributes(['id', 'name', 'avatar'])];
        } else {
            throw new HttpException(400, json_encode($model->getErrors()));
        }
    }
}

