<?php

namespace app\controllers;

use app\models\entities\Webhook;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class WebhookController extends Controller
{
    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::class,
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['GET', 'POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        if (Yii::$app->request->isGet) {
            return $this->verify();
        }

        if (Yii::$app->request->isPost) {
            return $this->receive();
        }

    }

    private function verify()
    {
        $request = Yii::$app->request;

        // Получаем параметры из запроса
        $hubMode = $request->get('hub_mode');
        $hubChallenge = $request->get('hub_challenge');
        $hubVerifyToken = $request->get('hub_verify_token');

        if ($hubMode === 'subscribe' && $hubVerifyToken === Yii::$app->params['instagramWebhookVerifyToken']) {
            return $hubChallenge;
        }

        return 'Verification token mismatch';
    }

    private function receive()
    {
        $data = Yii::$app->request->post();

        if (empty($data)) {
            Yii::$app->response->statusCode = 400;
            return 'Invalid webhook payload';
        }

        $model = new Webhook();
        $model->data = $data;
        if (!$model->save()) {
            Yii::$app->response->statusCode = 400;
            return 'Problem with webhook processing';
        }

        Yii::$app->response->statusCode = 200;
        return 'EVENT_RECEIVED';
    }
}