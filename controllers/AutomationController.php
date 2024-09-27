<?php

namespace app\controllers;

use app\filters\JwtAuthFilter;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\Controller;
use app\models\entities\Automation;
use yii\web\Response;

class AutomationController extends Controller
{
    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => JwtAuthFilter::class,
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'map' => ['GET'],
                    'create' => ['POST'],
                    'update' => ['PUT', 'PATCH'],
                    'delete' => ['DELETE'],
                ],
            ],
        ];
    }

    /**  Нужно отдавать Automation вот так
     *  {
     *      id:  int
     *      name: string
     *      trigger: {
     *          id: int
     *          type: string
     *          value: string
     *      }
     *      contents: {
     *          [id]: {
     *              id: int
     *              type: 'text'
     *              content: string
     *          }
     *          [id]: {
     *              id: int
     *              type: 'delay'
     *              duration: int
     *          }
     *      }
     *  }
     */
    public function actionMap()
    {
        $accountId = Yii::$app->request->get('account_id');

        if (!$accountId) {
            return ['status' => 'error', 'message' => 'Parameter account_id is required.'];
        }

        $automations = Automation::getList($accountId);

        $automationsById = [];

        foreach ($automations as $automation) {
            $automationId = $automation['id'];

            $contentsById = [];
            if (!empty($automation['contents'])) {
                foreach ($automation['contents'] as $content) {
                    $contentsById[$content['id']] = $content;
                }
            }

            $automationsById[$automationId] = [
                'id' => $automation['id'],
                'name' => $automation['name'],
                'trigger' => $automation['trigger'],
                'contents' => $contentsById,
            ];
        }


        return ['status' => 'success', 'automations' => $automationsById];
    }

    //  Перенести логику в модельку ActiveRecord или useCases
    public function actionCreate()
    {
        $accountId = Yii::$app->request->post('accountId');

        if (!$accountId) {
            return ['status' => 'error', 'message' => 'Parameter account_id is required.'];
        }

        try {
            [$automation, $trigger] = Automation::create($accountId);

            return ['status' => 'success', 'automation' => [...$automation, 'trigger' => $trigger, 'contents' => []]];
        } catch (\Exception $e) {

            return ['status' => 'error', 'error' => $e->getMessage()];
        }
    }

    public function actionUpdate()
    {
        $automationId = Yii::$app->request->post('automationId');
        $changes = Yii::$app->request->post('changes');

        if (!$automationId) {
            return ['status' => 'error', 'message' => 'Parameter automationId is required.'];
        }

        try {
            //            Не нужно ли здесь просто три сущности здесь создавать и обновлять
            //            Тогда и не будет этого updateValue
            //            И просто вызываем на сущностях апдейт, но что если связи между ними нужны? Например
            //            мы гарантируем что при обновлении автоматизации должен обновиться триггер?
            [$automation, $trigger, $contents] = Automation::updateValue($automationId, $changes);

            $contentsById = [];

            foreach ($contents as $content) {
                $contentsById[$content['id']] = $content;
            }

            return ['status' => 'success', 'automation' => [...$automation, 'trigger' => $trigger, 'contents' => $contentsById]];
        } catch (\Exception $e) {

            return ['status' => 'error', 'error' => $e->getMessage()];
        }
    }

    public function actionDelete()
    {
        $automationId = Yii::$app->request->get('automation_id');

        if (!$automationId) {
            return ['status' => 'error', 'message' => 'Parameter automationId is required.'];
        }

        try {
            $automation = Automation::deleteValue($automationId);

            return ['status' => 'success', 'automationId' => $automation->id];
        } catch (\Exception $e) {
            return ['status' => 'error', 'error' => $e->getMessage()];
        }
    }
}
