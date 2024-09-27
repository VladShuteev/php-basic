<?php

namespace app\models\entities;

use yii\db\ActiveRecord;

// Возможно стоит хранить данные в своей базе и не ходить в Meta
// особенно если там есть лимиты и потом по cron обновлять данные по
// аккаунту
/**
 * @property int $id
 * @property int $account_id
 * @property string $token
 * @property int $created_at
 */
class Channel extends ActiveRecord
{
}
