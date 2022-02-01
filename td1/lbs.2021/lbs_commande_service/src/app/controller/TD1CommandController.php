<?php

namespace lbs\command\app\controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \lbs\command\app\model\Commande as Commande;
use \lbs\command\app\model\Item as Item;
use \lbs\command\app\model\Paiement as Paiement;

use lbs\command\app\error\JsonError as JsonError;
use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;
use \lbs\command\app\controller\TD4CommandController as TD4CommandController;


class TD1CommandController{

    private $c; // le conteneur de dépendences de l'application

    public function __construct(\Slim\Container $c)
    {
        $this->c = $c;
    }


    public function listCommands(Request $req, Response $resp, array $args): Response{
        //get all the commands
        $commandes = Commande::select(['id', 'nom', 'mail', 'montant', 'livraison'])
            ->get();

        //complete the data array with datas who are gonna be returned in JSON format
        $data = [
            "type" => "collection",
            "count" => count($commandes),
            "commandes" => $commandes
        ];

        //configure the response headers
        $resp = $resp->withStatus(200)
            ->withHeader('Content-Type', 'application/json; charset=utf-8');


        //write in the body with data encode with a json_encode function
        $resp->getBody()->write(json_encode($data));

        //return the response (ALWAYS !)
        return $resp;
    }


    public function oneCommand(Request $req, Response $resp, array $args): Response {
        //get the id in the URI with the args array
        $id = $args['id'];
        
        //try & ctach in case the id doesn't exist
        try {

            //get the actual URI & params
            $uri = $req->getUri();
            $params = $req->getQueryParam('embed' , null);            

            //get the command with some id
            $commande = Commande::select(['id', 'nom', 'mail', 'montant', 'livraison'])
                ->where('id', '=', $id);
            if($params === 'items'){
                $commande = $commande->with('items');
            }    
            $commande = $commande->firstOrFail();

            //complete the data array with datas who are gonna be returned in JSON format
            $data = [
                "type" => "resource",
                "commande" => $commande,
                "links" => [
                    "items" => ["href" => $uri.'/items' ],
                    "self" => ["href" => "$uri" ]
                ]
            ];
            

            //configure the response headers
            $resp = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json; charset=utf-8');
    
            //write in the body with data encode with a json_encode function
            $resp->getBody()->write(json_encode($data));
            
            //return the response (ALWAYS !)
            return $resp;

        }
        //in case there is 0 ressource with this id ... 
        catch (ModelNotFoundException $e) {
            return JsonError::jsonError($req, $resp, 'error', 404,'Ressource not found : command ID = ' . $id );
        }
    }

}