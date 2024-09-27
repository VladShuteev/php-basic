<?php

namespace app\models\entities;

use app\enums\ContentType;
use \yii\db\ActiveRecord;

/**
 *
 * @property string $id
 * @property int $automation_id
 * @property ContentType $type
 * @property int $created_at
 */
class Content extends ActiveRecord
{

    public function getContentText()
    {
        return $this->hasOne(ContentText::class, ['content_id' => 'id']);
    }
    public function getContentDelay()
    {
        return $this->hasOne(ContentDelay::class, ['content_id' => 'id']);
    }
}
