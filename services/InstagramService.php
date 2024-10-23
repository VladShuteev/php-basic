<?php

namespace app\services;

use GuzzleHttp\Exception\GuzzleException;
use Yii;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use yii\web\HttpException;

class InstagramService
{
    private Client $client;

    public function __construct()
    {
        $baseUri = \Yii::$app->params['instagramAPI'];
        $this->client = new Client(['base_uri' => $baseUri]);
    }

    public function getProfile(string $accessToken): ?array
    {
        $cacheKey = 'getProfile' . md5($accessToken);
        $url = '/me?fields=id,username,profile_picture_url&access_token=' . $accessToken;

        $cachedProfile = Yii::$app->cache->get($cacheKey);
        if ($cachedProfile) {
            Yii::info('Profile data retrieved from cache.', __METHOD__);
            return $cachedProfile;
        }

        try {
            Yii::info('Request URL: ' . $url, __METHOD__);

            $response = $this->client->get($url);

            Yii::info('Response status: ' . $response->getStatusCode(), __METHOD__);
            Yii::info('Response body: ' . $response->getBody(), __METHOD__);

            $response = json_decode($response->getBody(), true);
            Yii::$app->cache->set($cacheKey, $response, 3600);

            return $response;
        } catch (RequestException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function webhookSubscribe(int $userId, string $accessToken)
    {
        $url = '/' . $userId . '/subscribed_apps';

        try {
            Yii::info('Request URL: ' . $url, __METHOD__);

            $response = $this->client->post($url, ['form_params' => [
                'access_token' => $accessToken,
                'subscribed_fields' => 'messages',
            ]]);

            Yii::info('Response status: ' . $response->getStatusCode(), __METHOD__);
            Yii::info('Response body: ' . $response->getBody(), __METHOD__);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function sendMessage($message, $recipientId)
    {
        Yii::info('Send Message: ' . $message . 'to' . $recipientId, __METHOD__);
    }
}
