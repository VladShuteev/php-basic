<?php

namespace app\controllers;

use app\filters\JwtAuthFilter;
use app\services\ProcessingService;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class ProController extends Controller
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
                    'fire' => ['POST'],
                ],
            ],
        ];
    }

    public function actionFire()
    {
        $post = Yii::$app->request->getBodyParams();

        ProcessingService::processMessage($post['text'], 'id');

    }

}
