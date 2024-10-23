<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class ContentDelayFixture extends ActiveFixture
{
    public $modelClass = 'app\models\entities\ContentDelay';
    public $dataFile = '@app/tests/_data/content_delay.php';

}
