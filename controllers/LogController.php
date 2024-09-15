<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class LogController extends Controller
{
    public function actionIndex()
    {
        $logFile = Yii::getAlias('@runtime/logs/app.log');

        if (file_exists($logFile)) {
            $logs = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $logs = array_reverse($logs);

            $reversedLogs = implode("\n", $logs);

            Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
            Yii::$app->response->headers->add('Content-Type', 'text/plain');

            return $reversedLogs;
        } else {
            return 'Log file not found';
        }
    }
}