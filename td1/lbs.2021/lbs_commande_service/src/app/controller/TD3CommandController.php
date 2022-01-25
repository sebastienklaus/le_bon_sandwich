<?php

namespace lbs\command\app\controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \lbs\command\app\model\Commande as Commande;
use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;


class TD3CommandController{

    private $c; // le conteneur de dépendences de l'application

    public function __construct(\Slim\Container $c)
    {
        $this->c = $c;
    }


    public function updateCommand(Request $req, Response $resp, array $args): Response {
        //get id of the command
        $id = $args['id'];
        
        
        //write in the body with data encode with a json_encode function
        $resp->getBody()->write(json_encode($id));
            
        //return the response (ALWAYS !)
        return $resp;
    }
}