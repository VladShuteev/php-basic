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

        Yii::info('Webhook received: ' . json_encode($data), __METHOD__);

//        $webhook = new Webhook();
//        $webhook->data = json_encode($data); // Сохраняем JSON данные в поле data

//        if ($webhook->save()) {
            Yii::$app->response->statusCode = 200; // Устанавливаем статус успешного ответа
            return ['status' => 'success', 'message' => 'Webhook data saved successfully'];
//        } else {
//            Yii::error('Failed to save webhook: ' . json_encode($webhook->getErrors()), __METHOD__);
//            Yii::$app->response->statusCode = 400; // Устанавливаем статус ошибки
//            return ['status' => 'error', 'message' => 'Failed to save webhook data'];
//        }
    }
}