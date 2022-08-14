<?php

namespace App\Controller;

use App\Lib\ApiErrors;
use App\Lib\ShortUrl;
use App\Lib\ValidateAuthorization;
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
    /** @var ValidateAuthorization */
    private $validateAuthorization;
    /** @var ShortUrl */
    private $shortUrl;
    /** @var ApiErrors */
    private $apiErrors;

    /**
     * @param HttpClientInterface $client
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->validateAuthorization = new ValidateAuthorization();
        $this->shortUrl = new ShortUrl($this);
        $this->apiErrors = new ApiErrors();
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
                return $this->json( $this->setError(JsonResponse::HTTP_UNAUTHORIZED, self::MSG_ERROR_TOKEN));
            }
        }

        $body = $request->getContent();
        $body_obj = json_decode($body);

        if(empty($body_obj->url)){
            return $this->json($this->setError(JsonResponse::HTTP_BAD_REQUEST, self::MSG_EMPTY_VALUE));
        }

        $violations = Validation::createValidator()->validate($body_obj->url, new Url());
        if(count($violations) >0){
            return $this->json($this->setError(JsonResponse::HTTP_BAD_REQUEST, $violations[0]->getMessage()));
        }
        $url_return =$this->getShortUrl($body_obj->url);
        if(!empty($url_return)){
            return $this->json([
                'url' => $url_return,
            ]);
        }else{
            return $this->json($this->setError(self::CODE_ERROR_TINYURL, self::MSG_ERROR_TINYURL));
        }
    }

    private function setError($code, $msg)
    {
        return $this->apiErrors->setError($code, $msg);
    }

    private function getShortUrl($url)
    {
        return $this->shortUrl->getShortUrl($url);
    }

    private function validateAuthorization($token)
    {
        return $this->validateAuthorization->validateAuthorization($token);
    }
}
