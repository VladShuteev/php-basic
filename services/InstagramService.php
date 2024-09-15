<?php

namespace app\services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use yii\web\HttpException;

class InstagramService {
    private $client;

    public function __construct()
    {
        $baseUri = \Yii::$app->params['instagramAPI'];
        $this->client = new Client(['base_uri' => $baseUri]);
    }

    /**
     * @param string $accessToken
     *
     * @return array{id: int, username: string, profile_picture_url: string} | null
     *
     * @throws \yii\web\HttpException
     */
    public function getProfile($accessToken)
    {
        $url = '/me?fields=id,username,profile_picture_url&access_token=' . $accessToken;

        try {
            $response = $this->client->get($url);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }
}