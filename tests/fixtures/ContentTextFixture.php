<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class ContentTextFixture extends ActiveFixture
{
    public $modelClass = 'app\models\entities\ContentText';
    public $dataFile = '@app/tests/_data/content_text.php';

}
