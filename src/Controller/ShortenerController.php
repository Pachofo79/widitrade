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
    const MSG_EMPTY_VALUE = 'Url is mandatory';
    const CODE_ERROR_TINYURL = 600;
    const MSG_ERROR_TINYURL = 'Something happend in tinyurl service';
    const MSG_ERROR_TOKEN = 'Not Authorized';

    /**
     * @var HttpClientInterface
     */
    public $client;

    /**
     * @param HttpClientInterface $client
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @Route("/api/v1/short-urls", name="app_shortener")
     */
    public function index(Request $request): JsonResponse
    {
        $token = $request->headers->get('authorization');
        if(!empty($token)){
            $token = str_replace( 'Bearer ', '', $token);
            if(!$this->validateAuthorization($token)){
                return $this->setError(JsonResponse::HTTP_UNAUTHORIZED, self::MSG_ERROR_TOKEN);
            }
        }

        $body = $request->getContent();
        $body_obj = json_decode($body);

        if(empty($body_obj->url)){
           return $this->setError(JsonResponse::HTTP_BAD_REQUEST, self::MSG_EMPTY_VALUE);
        }

        $violations = Validation::createValidator()->validate($body_obj->url, new Url());
        if(count($violations) >0){
            return $this->setError(JsonResponse::HTTP_BAD_REQUEST, $violations[0]->getMessage());
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

    /**
     * @param $code
     * @param $msg
     * @return mixed
     */
    private function setError($code, $msg){
        return $this->json([
            'success' => false,
            'error' => [
                'errCode' => $code,
                'errMsg' => $msg
            ]
        ]);
    }

    /**
     * @param $url
     * @return false|string
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
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

    /**
     * @param $token
     * @return bool
     */
    private function validateAuthorization($token)
    {
        $token = str_split($token);
        $stack = array();
        foreach($token as $value){

            switch ($value) {
                case '(': array_push($stack, 0); break;
                case ')':
                    if (array_pop($stack) !== 0)
                        return false;
                    break;
                case '[': array_push($stack, 1); break;
                case ']':
                    if (array_pop($stack) !== 1)
                        return false;
                    break;
                case '{': array_push($stack, 2); break;
                case '}':
                    if (array_pop($stack) !== 2)
                        return false;
                    break;
                default:
                    return false;
            }
        }
        return (empty($stack));
    }
}
