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
        //initiate variables for pagination
        $size = 10;
        $page = 0;
        $last_page = 0;

        //check if pagination required (get page number)
        if (isset($req->getQueryParams()['page']) != null && is_numeric($req->getQueryParams()['page']) && $req->getQueryParams()['page'] > 0) {
            $page = intval($req->getQueryParams()['page']);
        } else if($req->getQueryParams()['page'] < 0){

        }

        //check if pagination required (get size number)
        if (isset($req->getQueryParams()['size']) && is_numeric($req->getQueryParams()['size']) && $req->getQueryParams()['size'] > 0) {
            $size = intval($req->getQueryParams()['size']);
        }

        //1st part of the request
        $allCommands = Commande::select(['id', 'nom', 'created_at', 'livraison', 'status']);

        //check for status filter
        if (isset($req->getQueryParams()['s']) && is_numeric($req->getQueryParams()['s'])) {
            $allCommands = $allCommands->where('status', intval($req->getQueryParams()['s']));
        }

        //count all commands (before limit())
        $recordCount = $allCommands->count();


        //initiate the maximum page
        $last_page = intval($recordCount / $size);

        //check if actual page is greater than value of page maximum
        if($page > $last_page){
            $page = $last_page;
        }

        //Next page
        if ($page > 1) {
            if ($page < $last_page) {
                $nextPage = $page + 1;
            } else {
                $nextPage = $last_page;
            }
        } else {
            $nextPage = 2;
        }

        //Prev page
        if ($page > 1) {
            $prevPage = $page - 1;
        } else {
            $prevPage = 1;
        }

        //initiate offset value
        $offset = ($page - 1) * $size;

        //2nd part of the request with pagination
        $allCommands = $allCommands->limit($size)->offset($offset)->get();

        // initaite array for datas + links
        $command_and_links = [];

        //foreach lopp for each command
        foreach($allCommands as $commande){
            // get the uri path for each command
            $url_oneCommand = $this->container->router->pathFor('command', ['id'=>$commande['id']]);
            // add to another array the details of each command (data + links)
            $command_and_links[] = [
                'command' => [
                    'id' => $commande['id'],
                    'nom' => $commande['nom'],
                    'created_at' => $commande['created_at'],
                    'livraison' => $commande['livraison'],
                    'status' => $commande['status']
                ],
                'links' => [
                    'self' => [ 'href' => $url_oneCommand],
                    'next' => [ 'href' => $this->container->router->pathFor('allCommands',[],['page' => $nextPage,'size' => $size])],                            
                    'prev' => [ 'href' => $this->container->router->pathFor('allCommands',[],['page' => $prevPage,'size' => $size])],                            
                    'first'=> [ 'href' => $this->container->router->pathFor('allCommands',[],['page' => 1,'size' => $size])],                            
                    'last' => [ 'href' => $this->container->router->pathFor('allCommands',[],['page' => $last_page,'size' => $size])],
                ]
            ];
        };

        //complete the data array with datas who are gonna be returned in JSON format
        $data = [
            "type" => "collection",
            "count" => $recordCount,
            'size' => $size,
            "commandes" => $command_and_links,
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
            $url_oneCommand = $this->container->router->pathFor('command', ['id'=>$id]);
            // $url_itemsOfCommand = $this->container->router->pathFor('commandWithItems', ['id'=>$id]);
            $param_embed = $req->getQueryParam('embed' , null); 

            $param_token = $req->getAttribute('token');

            //get the command with some id
            $commande = Commande::select(['id', 'nom', 'created_at', 'livraison', 'mail', 'montant', 'livraison', 'token'])
                ->where('id', '=', $id);
            if($param_embed === 'items'){
                $commande = $commande->with('items:id,libelle,tarif,quantite,command_id');
            }    
            $commande = $commande->firstOrFail();


            //complete the data array with datas who are gonna be returned in JSON format
            $data = [
                "type" => "resource",
                "commande" => $commande,
                "links" => [
                    // "items" => ["href" => $url_itemsOfCommand ],
                    "self" => ["href" => "$url_oneCommand" ]
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
            $this->container->logger_debug->debug('GET / : debug (c\'est pas très grave, pas de gros soucis pour l\'instant');
            $this->container->logger_warning->warning('GET / : warning (au secours, tout va mal chef !');
            return JsonError::jsonError($req, $resp, 'error', 404,'Ressource not found : command ID = ' . $id );   

        }
    }


}