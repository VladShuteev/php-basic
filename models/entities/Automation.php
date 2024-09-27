<?php

namespace app\models\entities;

use app\enums\ContentType;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

/**
 * @property int $id
 * @property int $account_id
 * @property string $name
 * Это вообще JSON, нет ли более строгого типа для Active Record?
 * @property string $content
 * @property int $created_at
 */
class Automation extends ActiveRecord
{
    public function init()
    {
        parent::init();
        //      Нужно потом понять как делать по аккаунту или по триггеру.
        //      Сложно еще в том, чтобы не создавать с одним и тем же именем
        //      Может на основе id раз он целочисленный? Но если будет uui, то логика будет сложнее
        $this->name = 'Automation ' . (self::find()->count() + 1);
    }

    public static function create(int $accountId)
    {

        try {
            $transaction = Yii::$app->db->beginTransaction();

            $automation = new self();
            $automation->account_id = $accountId;

            if (!$automation->save()) {
                //                Вот здесь как-будто бы должна отдавать ошибка 400
                throw new Exception($automation->errors);
            }

            $trigger = new Trigger();
            $trigger->automation_id = $automation->id;
            $trigger->account_id = $accountId;

            if (!$trigger->save()) {
                throw new Exception($trigger->errors);
            }
            //            Создавать контент

            $transaction->commit();

            //          Нужно создать тип автоматизации в котором будет описана ожидаемая структура
            //          По сути, наверное, это моделька
            return [$automation, $trigger];
        } catch (Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
    }

    public static function getList(int $accountId)
    {
        //      Посмотреть как профилировать запросы к базе данных
        return self::find()
            ->where(['account_id' => $accountId])
            ->with([
               'trigger' => function ($query) {
                   $query->asArray();
               },
               'contents' => function ($query) {
                   $query->asArray();
               },
           ])
            ->asArray()
            ->all();
    }

    public static function updateValue(int $automationId, $changes)
    {
        try {
            $transaction = Yii::$app->db->beginTransaction();

            $automation = self::find()
                ->where(['id' => $automationId])
                ->with(['trigger', 'contents.contentText', 'contents.contentDelay'])
                ->one();

            if (!$automation) {
                return ['status' => 'error', 'message' => 'Automation not found'];
            }

            if (isset($changes['name'])) {
                $automation->name = $changes['name'];
            }

            if (isset($changes['trigger'])) {
                if (isset($changes['trigger']['value'])) {
                    $automation->trigger->value = $changes['trigger']['value'];
                    $automation->trigger->save();
                }
            }

            if (isset($changes['content'])) {
                //                Мне нужно найти в массиве Automation->contents id контента, если его там нет,
                //                то создать новый
                $findedContent = null;
                foreach ($automation->contents as $content) {
                    if ($changes['content']['id'] == $content['id']) {
                        $findedContent = $content;
                    }
                }

                if ($findedContent) {
                    switch ($findedContent->type) {
                        case ContentType::TEXT->value: {
                            if (isset($changes['content']['content'])) {
                                $findedContent->contentText->content = $changes['content']['content'];
                                $findedContent->contentText->save();
                            }
                        }
                            break;
                        case ContentType::DELAY->value: {
                            if (isset($changes['content']['duration'])) {
                                $findedContent->contentDelay->duration = $changes['content']['duration'];
                                $findedContent->contentDelay->save();
                            }
                        }
                            break;
                    }
                } else {
                    $content = new Content();
                    //                    Я не уверен, что полагаться на Id фронта можно. Как этот кейс обработать?
                    $content->id = $changes['content']['id'];
                    $content->automation_id = $automationId;
                    $content->type = $changes['content']['type'];

                    $content->save();

                    switch ($changes['content']['type']) {
                        case ContentType::TEXT->value: {
                            $text = new ContentText();
                            $text->content_id = $changes['content']['id'];
                            $text->content = $changes['content']['content'];
                            $text->save();
                        }
                            break;
                        case ContentType::DELAY->value: {
                            $delay = new ContentDelay();
                            $delay->content_id = $changes['content']['id'];
                            $delay->duration = $changes['content']['duration'];
                            $delay->save();
                        }
                            break;

                    }

                }
            }

            $automation->save();

            $transaction->commit();

            return [$automation, $automation->trigger, $automation->contents];

        } catch (Exception $ex) {
            $transaction->rollBack();

            throw $ex;
        }
    }

    public static function deleteValue(int $automationId)
    {
        try {
            $transaction = Yii::$app->db->beginTransaction();

            $automation = self::find()
                ->where(['id' => $automationId])
                ->with('trigger')
                ->one();

            //            Также удалять content
            $automation->trigger->delete();
            $automation->delete();

            $transaction->commit();


            return $automation;
        } catch (Exception $ex) {
            $transaction->rollBack();

            throw $ex;
        }
    }

    public function getTrigger()
    {
        return $this->hasOne(Trigger::class, ['automation_id' => 'id']);
    }

    public function getContents()
    {
        return $this->hasMany(Content::class, ['automation_id' => 'id']);
    }
}
