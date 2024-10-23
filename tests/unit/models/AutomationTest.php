<?php

namespace app\tests\unit\models;

use app\models\entities\Automation;
use app\tests\fixtures\AutomationFixture;
use Codeception\Test\Unit;

class AutomationTest extends Unit
{
    protected $tester;

    public function _fixtures()
    {
        return [
            'automations' => [
                'class' => AutomationFixture::class,
                'dataFile' => '@app/tests/_data/automation.php'
            ],
        ];
    }

    public function testUpdateAutomationName()
    {
        $automationId = 1;
        $newName = 'Updated Automation Name';

        Automation::updateValue($automationId, ['name' => $newName]);

        $automation = Automation::findOne($automationId);
        $this->assertEquals($newName, $automation->name);
    }

    public function testErrorWhenItIsNotFound()
    {
        $automationId = 12434;
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Automation not found'); // Сообщение может быть любым, в зависимости от того, что выбрасывает метод

        Automation::updateValue($automationId, ['name' => 'Updated Automation Name']);

        $automation = Automation::findOne($automationId);
        $this->assertNull($automation, 'Automation should not exist.');
    }
}
