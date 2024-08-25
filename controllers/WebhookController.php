<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;

class WebhookController extends Controller
{
    // Отключаем защиту CSRF для этого контроллера, так как Instagram отправляет POST-запросы без CSRF-токена
    public $enableCsrfValidation = false;

    /**
     * Метод для верификации Webhook
     */
    public function actionVerify()
    {
        $request = Yii::$app->request;

        // Получаем параметры из запроса
        $hubMode = $request->get('hub_mode');
        $hubChallenge = $request->get('hub_challenge');
        $hubVerifyToken = $request->get('hub_verify_token');

        // Проверяем, совпадает ли переданный токен с нашим
        if ($hubMode === 'subscribe' && $hubVerifyToken === Yii::$app->params['instagramWebhookVerifyToken']) {
            // Возвращаем challenge для подтверждения
            return $hubChallenge;
        }

        return 'Verification token mismatch';
    }

    /**
     * Метод для обработки событий Webhook
     */
    public function actionReceive()
    {
        $request = Yii::$app->request;

        // Получаем входящие данные
        $data = $request->post();

        // Логируем данные для отладки (не забудьте удалить это на продакшене)
        Yii::error('Webhook received: ' . json_encode($data));

        // Здесь добавьте логику обработки полученных данных
        // Например, запись в базу данных, отправка уведомлений и т.д.

        // Возвращаем успешный ответ
        Yii::$app->response->statusCode = 200;
        return 'EVENT_RECEIVED';
    }
}