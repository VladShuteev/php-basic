<?php

namespace app\services;

use app\models\entities\Channel;
use app\models\entities\Trigger;
use app\jobs\ProcessAutomationJob;
use Yii;

class ProcessingService
{
    public static function processMessage($message, $recipientId, $metaId)
    {
        try {
            $keywords = Trigger::find()->where(['type' => 'keyword', 'value' => $message])->all();
            $channel = Channel::find()->where(['meta_id' => $metaId])->one();

            if ($channel === null) {
                throw new \Exception('Channel not found');
            }

            foreach ($keywords as $keyword) {
                Yii::$app->queue->push(new ProcessAutomationJob(
                    [
                        'automationId' => $keyword->automation_id,
                        'recipientId' => $recipientId,
                        'channelToken' => $channel->token,
                    ]
                ));
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
