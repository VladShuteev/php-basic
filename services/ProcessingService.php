<?php

namespace app\services;

use app\models\entities\Trigger;
use app\jobs\ProcessAutomationJob;
use Yii;

class ProcessingService
{
    public static function processMessage($message, $recipientId)
    {
        try {
            $keywords = Trigger::find()->where(['type' => 'keyword', 'value' => $message])->all();
            foreach ($keywords as $keyword) {
                Yii::$app->queue->push(new ProcessAutomationJob(
                    [
                        'automationId' => $keyword->automation_id,
                        'recipientId' => $recipientId
                    ]
                ));
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
