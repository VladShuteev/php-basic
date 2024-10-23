<?php

namespace tests\unit\jobs;

use app\jobs\ProcessAutomationJob;
use app\models\entities\Automation;
use app\models\entities\Content;
use app\models\entities\ContentText;
use app\services\InstagramService;
use app\tests\fixtures\AutomationFixture;
use app\tests\fixtures\ContentDelayFixture;
use app\tests\fixtures\ContentFixture;
use app\tests\fixtures\ContentTextFixture;
use Yii;
use yii\queue\sync\Queue;

class ProcessAutomationJobTest extends \Codeception\Test\Unit
{
    protected $tester;

    protected function _before()
    {
        // Устанавливаем синхронную очередь для тестирования
        Yii::$app->set('queue', [
            'class' => Queue::class,
        ]);

        // Загружаем фикстуры
        $this->tester->haveFixtures([
            'automations' => [
                'class' => AutomationFixture::class,
                'dataFile' => codecept_data_dir() . 'automation.php',
            ],
            'contents' => [
                'class' => ContentFixture::class,
                'dataFile' => codecept_data_dir() . 'content.php',
            ],
            'contentTexts' => [
                'class' => ContentTextFixture::class,
                'dataFile' => codecept_data_dir() . 'content_text.php',
            ],
            'contentDelays' => [
                'class' => ContentDelayFixture::class,
                'dataFile' => codecept_data_dir() . 'content_delay.php',
            ],
        ]);
    }

    public function testExecuteWithTextContent()
    {
        // Мокируем InstagramService
        $instagramServiceMock = $this->createMock(InstagramService::class);
        $instagramServiceMock->expects($this->once())
            ->method('sendMessage')
            ->with($this->equalTo('Hello, World!'));

        // Заменяем сервис в контейнере
        Yii::$container->set(InstagramService::class, $instagramServiceMock);

        // Создаём задание
        $job = new ProcessAutomationJob([
            'automationId' => 1,
        ]);

        // Выполняем задание
        $job->execute(Yii::$app->queue);
    }

    public function testExecuteWithDelayContent()
    {
        // Настраиваем очередь для отслеживания заданий
        $queue = new Queue(['handle' => false]);
        Yii::$app->set('queue', $queue);

        // Создаём задание с контентом типа DELAY
        $job = new ProcessAutomationJob([
            'automationId' => 1,
            'contentId' => 'content_delay_1',
        ]);

        // Выполняем задание
        $job->execute($queue);

        // Проверяем, что новое задание было поставлено в очередь
        $this->assertCount(1, $queue->jobs);

        // Получаем задание из очереди
        $newJob = $queue->jobs[0]['job'];

        // Проверяем, что это задание ProcessAutomationJob с правильным contentId
        $this->assertInstanceOf(ProcessAutomationJob::class, $newJob);
        $this->assertEquals('content_text_2', $newJob->contentId);
    }

    public function testExecuteThrowsExceptionWhenAutomationNotFound()
    {
        $this->expectException(\yii\base\ErrorException::class);
        $this->expectExceptionMessage('Automation not found');

        $job = new ProcessAutomationJob([
            'automationId' => 999, // Несуществующая автоматизация
        ]);

        $job->execute(Yii::$app->queue);
    }

    public function testExecuteThrowsExceptionWhenContentNotFound()
    {
        $this->expectException(\yii\base\ErrorException::class);
        $this->expectExceptionMessage('content not found');

        $job = new ProcessAutomationJob([
            'automationId' => 1,
            'contentId' => 'non_existing_content',
        ]);

        $job->execute(Yii::$app->queue);
    }
}
