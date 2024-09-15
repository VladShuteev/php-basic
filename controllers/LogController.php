<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class LogController extends Controller
{
    public function actionIndex()
    {
        // Путь к логам
        $logFile = Yii::getAlias('@runtime/logs/app.log');

        if (file_exists($logFile)) {
            // Получаем содержимое файла
            $logs = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            // Переворачиваем порядок строк, чтобы последние логи были вверху
            $logs = array_reverse($logs);

            // Собираем строки обратно в текст
            $reversedLogs = implode("\n", $logs);

            // Устанавливаем заголовки для отображения текста
            Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
            Yii::$app->response->headers->add('Content-Type', 'text/plain');

            // Возвращаем перевернутые логи
            return $reversedLogs;
        } else {
            return 'Log file not found';
        }
    }
}