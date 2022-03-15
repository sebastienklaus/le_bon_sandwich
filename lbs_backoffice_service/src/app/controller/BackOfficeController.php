<?php

namespace lbs\backoffice\app\controller;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException ;
use Firebase\JWT\BeforeValidException;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\backoffice\app\model\User as User;
use lbs\backoffice\app\error\Writer as Writer;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use GuzzleHttp\Client as Client;

class BackOfficeController
{

    private $container; // le conteneur de dépendences de l'application
    

    public function __construct(\Slim\Container $container)
    {
        $this->container = $container;
    }


    public function authenticate(Request $req, Response $resp, $args): Response {

        $client = new Client([
            'base_uri' => $this->container->get('settings')['auth_service'],
            'timeout' => 5.0
            ]);
        
        $response = $client->request('POST', '/auth', [
                'headers'=> ['Authorization' => $req->getHeader('Authorization')]
            ]
        );

        $resp = $resp->withStatus($response->getStatusCode())
        ->withHeader('Content-Type', $response->getHeader('Content-Type'))
        ->withBody($response->getBody());

        return $resp;

    }


}