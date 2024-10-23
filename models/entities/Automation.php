<?php

namespace app\models\entities;

use app\enums\ContentType;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * @property int $id
 * @property int $account_id
 * @property string $name
 * @property string $content_id
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
            ->with(
                'trigger',
                'activeContents.contentText',
                'activeContents.contentDelay'
            )
            ->asArray()
            ->all();
    }

    public static function updateValue(int $automationId, $changes)
    {
        try {
            $transaction = Yii::$app->db->beginTransaction();

            $automation = self::find()
                ->where(['id' => $automationId])
                ->with(
                    'trigger',
                    'activeContents.contentText',
                    'activeContents.contentDelay'
                )
                ->one();

            if (!$automation) {
                throw new NotFoundHttpException('Automation not found');
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
                $foundContent = null;
                //                N+1??
                foreach ($automation->activeContents as $content) {
                    if ($changes['content']['id'] == $content['id']) {
                        $foundContent = $content;
                    }
                }

                if ($foundContent) {
                    if (isset($changes['content']['isDeleted'])) {
                        $foundContent->is_deleted = $changes['content']['isDeleted'];

                        //                        На это нужны тесты
                        if (count($automation->activeContents) == 1) {
                            if ($automation->content_id == $foundContent->id) {
                                $automation->content_id = null;
                            }
                        } else {
                            $filteredArray = array_filter($automation->activeContents, function ($content) use ($automation) {
                                return $content->id === $automation->content_id;
                            });
                            $cursor = array_pop($filteredArray);

                            //                            Если в начале списка
                            if ($cursor->id === $foundContent->id) {
                                $automation->content_id = $cursor->next_content_id;
                            } else {
                                while ($cursor->next_content_id != null) {
                                    $filteredArray = array_filter($automation->activeContents, function ($content) use ($cursor) {
                                        return $cursor->next_content_id === $content->id;
                                    });
                                    $prevCursor = $cursor;
                                    $cursor = array_pop($filteredArray);
                                    if ($cursor->id === $foundContent->id) {
                                        if ($cursor->next_content_id != null) {
                                            //                            Если в середине списка
                                            $prevCursor->next_content_id = $cursor->next_content_id;
                                            $cursor->next_content_id = null;
                                        } else {
                                            //                            Если в конце списка
                                            $prevCursor->next_content_id = null;
                                        }
                                    }
                                    $prevCursor->save();
                                }



                            }

                            $cursor->save();
                        }
                    }
                    switch ($foundContent->type) {
                        case ContentType::TEXT->value: {
                            if (isset($changes['content']['content'])) {
                                $foundContent->contentText->content = $changes['content']['content'];
                                $foundContent->contentText->save();
                            }
                        }
                            break;
                        case ContentType::DELAY->value: {
                            if (isset($changes['content']['duration'])) {
                                $foundContent->contentDelay->duration = $changes['content']['duration'];
                                $foundContent->contentDelay->save();
                            }
                        }
                            break;
                    }

                    $foundContent->save();
                } else {
                    $content = new Content();
                    //                    Я не уверен, что полагаться на Id фронта можно. Как этот кейс обработать?
                    $content->id = $changes['content']['id'];
                    $content->automation_id = $automationId;
                    $content->type = $changes['content']['type'];

                    if (!$automation->content_id) {
                        $automation->content_id = $content->id;
                    } else {
                        $filteredArray = array_filter($automation->activeContents, function ($content) use ($automation) {
                            return $content->id === $automation->content_id;
                        });
                        $cursor = array_pop($filteredArray);

                        while ($cursor->next_content_id != null) {
                            $filteredArray = array_filter($automation->activeContents, function ($content) use ($cursor) {
                                return $content->id === $cursor->next_content_id;
                            });
                            $cursor = array_pop($filteredArray);
                        }

                        $cursor->next_content_id = $content->id;
                        $cursor->save();
                    }


                    $content->save();

                    switch ($changes['content']['type']) {
                        case ContentType::TEXT->value: {
                            $text = new ContentText();
                            $text->content_id = $content->id;
                            $text->content = $changes['content']['content'];
                            $text->save();
                        }
                            break;
                        case ContentType::DELAY->value: {
                            $delay = new ContentDelay();
                            $delay->content_id = $content->id;
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
                ->with(
                    'trigger',
                    'contents.contentText',
                    'contents.contentDelay'
                )
                ->one();

            if (!$automation) {
                throw new NotFoundHttpException('Automation not found');
            }

            //            Нельзя ли удалять сразу одним скоупом, не будет ли здесь проблемы N+1?
            foreach ($automation->contents as $content) {
                $content->delete();
            }
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

    public function getActiveContents()
    {
        return $this->hasMany(Content::class, ['automation_id' => 'id'])
            ->alias('c')
            ->andOnCondition(['c.is_deleted' => false]);
    }
    public function getContents()
    {
        return $this->hasMany(Content::class, ['automation_id' => 'id']);
    }
}
