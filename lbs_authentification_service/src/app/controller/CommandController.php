<?php

namespace lbs\auth\app\controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \lbs\auth\app\model\User as User;

use lbs\auth\app\error\JsonError as JsonError;
use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;



class CommandController{

    private $container; // le conteneur de dépendences de l'application
    

    public function __construct(\Slim\Container $container)
    {
        $this->container = $container;
    }

    public function hello(Request $req, Response $resp, array $args): Response {
        //configure the response headers
        $resp = $resp->withStatus(200)
        ->withHeader('Content-Type', 'application/json; charset=utf-8');
    
        //write in the body with data encode with a json_encode function
        $resp->getBody()->write(json_encode('hello'));

        //return the response (ALWAYS !)
        return $resp;
    }
}