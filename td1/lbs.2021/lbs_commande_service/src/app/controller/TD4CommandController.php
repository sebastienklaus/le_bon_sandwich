<?php

namespace lbs\command\app\controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \lbs\command\app\model\Commande as Commande;
use \lbs\command\app\model\Item as Item;
use \lbs\command\app\model\Paiement as Paiement;

use lbs\command\app\error\JsonError as JsonError;
use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;


class TD4CommandController{

    private $c; // le conteneur de dépendences de l'application

    public function __construct(\Slim\Container $c)
    {
        $this->c = $c;
    }


    public function getItemsOfCommand(Request $req, Response $resp, array $args): Response{
        $id = $args['id'];
        try {
            //get all the commands
        $commande = Commande::findOrFail($id);
        $items = $commande->items()
                ->select(['id', 'libelle', 'tarif', 'quantite'])
                ->get();

        //complete the data array with datas who are gonna be returned in JSON format
        $data = [
            "test" => $items[0]['id'],
            "type" => "collection",
            "count" => count($items),
            "items" => $items
        ];

        //configure the response headers
        $resp = $resp->withStatus(200)
            ->withHeader('Content-Type', 'application/json; charset=utf-8');


        //write in the body with data encode with a json_encode function
        $resp->getBody()->write(json_encode($data));

        //return the response (ALWAYS !)
        return $resp;
        } catch (ModelNotFoundException $e) {
            return JsonError::jsonError($req, $resp, 'error', 404,'Ressource not found : command ID = ' . $id );
        }
        
    }

}