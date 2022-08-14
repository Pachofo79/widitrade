<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\HttpClient\HttpClientInterface;





class ShortenerController extends AbstractController
{
    const CODE_ERROR_BADREQUEST = 401;
    const MSG_EMPTY_VALUE = 'Url is mandatory';
    const CODE_ERROR_TINYURL = 600;
    const MSG_ERROR_TINYURL = 'Something happend in tinyurl service.';

    public $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @Route("/api/v1/short-urls", name="app_shortener")
     */
    public function index(Request $request): JsonResponse
    {
        $body = $request->getContent();
        $body_obj = json_decode($body);

        if(empty($body_obj->url)){
           return $this->setError(self::CODE_ERROR_BADREQUEST, self::MSG_EMPTY_VALUE);
        }

        $violations = Validation::createValidator()->validate($body_obj->url, new Url());
        if(count($violations) >0){
            return $this->setError(401, $violations[0]->getMessage());
        }
        $url_return =$this->getShortUrl($body_obj->url);
        if(!empty($url_return)){
            return $this->json([
                'url' => $url_return,
            ]);
        }else{
            return $this->setError(self::CODE_ERROR_TINYURL, self::MSG_ERROR_TINYURL);
        }
    }

    private function setError($code, $msg){
        return $this->json([
            'success' => false,
            'error' => [
                'errCode' => $code,
                'errMsg' => $msg
            ]
        ]);
    }

    private function getShortUrl($url){
        $response = $this->client->request(
            'GET',
            'https://tinyurl.com/api-create.php?url='.$url
        );

        if($response->getStatusCode()==200){
            return $response->getContent();
        }else{
            return false;
        }

    }
}
