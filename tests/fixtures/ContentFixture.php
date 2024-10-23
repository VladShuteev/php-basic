<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class ContentFixture extends ActiveFixture
{
    public $modelClass = 'app\models\entities\Content';
    public $dataFile = '@app/tests/_data/content.php';

}
