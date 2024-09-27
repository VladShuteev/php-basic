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

class AccountController extends Controller
{
    
    public function behaviors()
    {
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
                    'list' => ['GET'],
                ],
            ],
        ];
    }

    private function getCacheKey(int $userId)
    {
        return 'AccountList' . $userId;
    }

    public function actionList(): array
    {
        $userId = Yii::$app->user->id;
        $cachedAccounts = Yii::$app->cache->get($this->getCacheKey($userId));
        if ($cachedAccounts) {
            Yii::info('Account list retrieved from cache.', __METHOD__);
            return $cachedAccounts;
        }

        $accounts = Account::find()
            ->select(['id', 'name', 'avatar'])
            ->where(['user_id' => $userId])
            ->all();
        Yii::$app->cache->set($this->getCacheKey($userId), $accounts, 3600);

        return $accounts;
    }

    public function actionCreate(): array
    {
        $userId = Yii::$app->user->id;
        $model = new Account();
        $model->user_id = $userId;
        $model->name = Yii::$app->request->post('name');
        $model->avatar = Yii::$app->request->post('avatar');

        if ($model->validate()) {
            Yii::$app->cache->delete($this->getCacheKey($userId));
            $model->save();
            return ['status' => 'success', 'message' => 'Account created', 'account' => $model->getAttributes(['id', 'name', 'avatar'])];
        } else {
            throw new HttpException(400, json_encode($model->getErrors()));
        }
    }
}
