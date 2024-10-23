<?php

namespace app\controllers;

use app\models\entities\Webhook;
use app\services\ProcessingService;
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

        $parsedData = json_encode($data);

        //        TODO: [0] хак, убрать
        if (isset($parsedData['entry'][0]['messaging'][0]['message']['text'])) {
            $message = $parsedData['entry'][0]['messaging'][0]['message']['text'];
            $recipientId = $parsedData['entry'][0]['messaging'][0]['sender']['id'];
            $metaId = $parsedData['entry'][0]['id'];
            ProcessingService::processMessage($message, $recipientId, $metaId);
        }

        Yii::$app->response->statusCode = 200;
        return ['status' => 'success', 'message' => 'Webhook data saved successfully'];
    }
}
