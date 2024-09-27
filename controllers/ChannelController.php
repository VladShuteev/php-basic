<?php

namespace app\controllers;

use app\filters\JwtAuthFilter;
use app\models\entities\Channel;
use app\services\InstagramService;
use GuzzleHttp\Exception\GuzzleException;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;

class ChannelController extends Controller
{
    private $instagramService;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->instagramService = new InstagramService();
    }

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
                    'profile' => ['GET'],
                    'connect' => ['POST'],
                ],
            ],
        ];
    }

    //    Нужно добавить проверку что запрашиваемый аккаунт есть у user
    public function actionProfile()
    {
        $accountId = Yii::$app->request->get('account_id');
        //        Потом для списка обработать
        $channel = Channel::find()->where(['account_id' => $accountId])->one();

        if (!$channel) {
            return null;
        }

        return $this->instagramService->getProfile($channel->token);
    }

    //    А что если у меня появится FBService как мне их обрабатывать правильно?

    /**
     * @throws GuzzleException
     * @throws HttpException
     */
    public function actionConnect()
    {
        $token = Yii::$app->request->post('token');
        $accountId = Yii::$app->request->post('accountId');

        try {
            $channel = new Channel();
            $channel->token = $token;
            $channel->account_id = $accountId;

            $profile = $this->instagramService->getProfile($token);

            if (!$profile) {
                return ['status' => 'error', 'errors' => 'profile not found'];
            }

            if (!$channel->save()) {
                return ['status' => 'error', 'errors' => $channel->getErrors()];
            }

            //            Нужна транзакция
            $this->instagramService->webhookSubscribe($profile['id'], $token);

            return ['status' => 'success', 'profile' => $profile];
        } catch (\Exception $e) {
            throw new HttpException(400, $e->getMessage());
        }
    }
}
