<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class AutomationFixture extends ActiveFixture
{
    public $modelClass = 'app\models\entities\Automation';
    public $dataFile = '@app/tests/_data/automation.php';

}
