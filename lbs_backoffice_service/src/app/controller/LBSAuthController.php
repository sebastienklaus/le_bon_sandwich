<?php

namespace lbs\backoffice\app\controller;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException ;
use Firebase\JWT\BeforeValidException;

use Illuminate\Database\Eloquent\ModelNotFoundException;
// use lbs\auth\app\model\User;
use lbs\backoffice\app\error\Writer as Writer;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


class LBSAuthController
{

    private $container; // le conteneur de dépendences de l'application
    

    public function __construct(\Slim\Container $container)
    {
        $this->container = $container;
    }


    public function hello(Request $req, Response $resp, $args): Response {

        $data = [
            'access-token' => '$token',
            'refresh-token' => '$user->refresh_token'
        ];

        return Writer::json_output($resp, 200, $data);

        return $resp;


    }


}