<?php

namespace app\models\entities;

use yii\db\ActiveRecord;

// Специально не буду добавлять account_id здесь, чтобы потом можно было сделать миграцию
// Миграция будет брать automation по automation_id, брать оттуда account_id и вставлять его в таблицу
/**
 * @property int $id
 * Это поле не нужно
 * @property int $account_id
 * @property int $automation_id
 * Тут наверное лучше использовать enum
 * @property string $type
 * @property string $value
 * @property int $created_at
 */
class Trigger extends ActiveRecord
{
    public function init()
    {
        parent::init();
        $this->type = 'keyword';
    }

}