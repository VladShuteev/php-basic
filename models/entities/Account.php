<?php

namespace app\models\entities;

use yii\db\ActiveRecord;

/**
 * Думаю что id должен быть uui, а не просто перечислением
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $avatar
 * @property int $created_at
 */
class Account extends ActiveRecord {

    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['name'], 'required'],
            [['name'], 'string', 'min' => 1, 'max' => 255],
            [['avatar'], 'required'],
        ];
    }

    public function init()
    {
        parent::init();
        $this->name = 'Untitled';
        $this->avatar = '/placeholder.svg?height=40&width=40&text=v';
    }
}