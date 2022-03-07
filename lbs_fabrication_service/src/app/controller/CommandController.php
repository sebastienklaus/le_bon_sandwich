<?php

namespace lbs\fab\app\controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \lbs\fab\app\model\Commande as Commande;
use \lbs\fab\app\model\Item as Item;
use \lbs\fab\app\model\Paiement as Paiement;

use lbs\fab\app\error\JsonError as JsonError;
use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;



class CommandController{

    private $container; // le conteneur de dépendences de l'application

    public function __construct(\Slim\Container $container)
    {
        $this->container = $container;
    }
    public function allCommands(Request $req, Response $resp, array $args): Response{
        //get all the commands
        $commandes = Commande::select(['id', 'nom', 'mail', 'montant', 'livraison'])
            ->get();

        //complete the data array with datas who are gonna be returned in JSON format
        $data = [
            "type" => "collection",
            "count" => count($commandes),
            "commandes" => $commandes,
        ];

        //configure the response headers
        $resp = $resp->withStatus(200)
            ->withHeader('Content-Type', 'application/json; charset=utf-8');


        //write in the body with data encode with a json_encode function
        $resp->getBody()->write(json_encode($data));
        

        //return the response (ALWAYS !)
        return $resp;
    }


}