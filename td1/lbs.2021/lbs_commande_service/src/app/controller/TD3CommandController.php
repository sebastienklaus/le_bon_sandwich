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


    public function replaceCommand(Request $req, Response $resp, array $args): Response {
        //get id of the command
        $id = $args['id'];


        $command_data = $req->getParsedBody();

        if(!isset($command_data['nom_client'])){
            //return error in json with 400 error code with msg 'missing data'
        }

        /*
        - try & catch

        -Command with findOrfail

        -catch modelnotfoundexception : error 404
        - catch exception error 500

        */



        


        
        
        //write in the body with data encode with a json_encode function
        $resp->getBody()->write(json_encode($id));
            
        //return the response (ALWAYS !)
        return $resp;
    }
}