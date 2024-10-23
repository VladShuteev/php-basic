<?php

namespace app\jobs;

use app\enums\ContentType;
use app\models\entities\Content;
use app\services\InstagramService;
use Yii;
use app\models\entities\Automation;
use yii\base\BaseObject;
use yii\base\ErrorException;
use yii\queue\JobInterface;

class ProcessAutomationJob extends BaseObject implements JobInterface
{
    public $automationId;
    public $contentId = null;
    //    Не нравится, что нужно прокидывать сюда, лучше будет прокидывать Instagram Service
    public $recipientId;

    public function execute($queue)
    {
        $currentContentId = $this->contentId;

        if ($this->contentId === null) {
            $automation = Automation::findOne($this->automationId);

            if ($automation === null) {
                throw new ErrorException('Automation not found');
            }

            $currentContentId = $automation->content_id;

            if ($currentContentId === null) {
                Yii::error('Response body: Automation without content', __METHOD__);
                return false;
            }
        }

        $content = Content::find()
            ->where(['id' => $currentContentId])
            ->with('contentText', 'contentDelay')
            ->one();
        if ($content === null) {
            throw new ErrorException('content not found');
        }

        switch ($content->type) {
            case ContentType::TEXT->value: {
                $instagramService = new InstagramService();
                $message = $content->contentText->content;

                $instagramService->sendMessage($message, $this->recipientId);
            }
                break;
            case ContentType::DELAY->value: {
                if ($content->next_content_id) {
                    Yii::$app->queue->push(new ProcessAutomationJob([
                        'automationId' => $this->automationId,
                        'contentId' => $content->next_content_id,
                        'recipientId' => $this->recipientId,
                    ]));
                }
                return;
            }
                break;
        }

        if ($content->next_content_id) {
            Yii::$app->queue->push(new ProcessAutomationJob([
                'automationId' => $this->automationId,
                'contentId' => $content->next_content_id,
            ]));
        }
    }
}
