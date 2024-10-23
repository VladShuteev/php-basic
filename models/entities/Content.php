<?php

namespace app\models\entities;

use app\enums\ContentType;
use \yii\db\ActiveRecord;

/**
 *
 * @property string $id
 * @property string $next_content_id
 * @property int $automation_id
 * @property ContentType $type
 * @property bool $is_deleted // CRON на удаление через N дней
 * @property int $created_at
 */
class Content extends ActiveRecord
{

    public function init()
    {
        parent::init();
        $this->is_deleted = false;

    }

    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        if ($this->contentText) {
            $this->contentText->delete();
        }
        if ($this->contentDelay) {
            $this->contentDelay->delete();
        }

        return true;
    }

    public function getContentText()
    {
        return $this->hasOne(ContentText::class, ['content_id' => 'id']);
    }
    public function getContentDelay()
    {
        return $this->hasOne(ContentDelay::class, ['content_id' => 'id']);
    }
}
