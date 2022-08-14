<?php

namespace App\Lib;

use App\Controller\ShortenerController;

class ShortUrl
{
    private $shortenerController;

    public function __construct(ShortenerController $shortenerController)
    {
        $this->shortenerController = $shortenerController;
    }

    /**
     * @param $url
     * @return false|string
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getShortUrl($url)
    {
        $response = $this->shortenerController->client->request(
            'GET',
            'https://tinyurl.com/api-create.php?url=' . $url
        );

        if ($response->getStatusCode() == 200) {
            return $response->getContent();
        } else {
            return false;
        }
    }
}