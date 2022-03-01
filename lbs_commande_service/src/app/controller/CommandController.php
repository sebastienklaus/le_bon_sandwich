<?php

namespace lbs\command\app\controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \lbs\command\app\model\Commande as Commande;
use \lbs\command\app\model\Item as Item;
use \lbs\command\app\model\Paiement as Paiement;

use lbs\command\app\error\JsonError as JsonError;
use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;

use DateTime;


class CommandController{

    private $c; // le conteneur de dépendences de l'application

    public function __construct(\Slim\Container $c)
    {
        $this->c = $c;
    }

    // TD1 & 2
    public function listCommands(Request $req, Response $resp, array $args): Response{
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

    // TD 1,2 & 4
    public function oneCommand(Request $req, Response $resp, array $args): Response {
        //get the id in the URI with the args array
        $id = $args['id'];
        
        //try & ctach in case the id doesn't exist
        try {

            //get the actual URI & params
            $url_oneCommand = $this->c->router->pathFor('command', ['id'=>$id]);
            $url_itemsOfCommand = $this->c->router->pathFor('commandWithItems', ['id'=>$id]);
            $params = $req->getQueryParam('embed' , null);            

            //get the command with some id
            $commande = Commande::select(['id', 'nom', 'created_at', 'livraison', 'mail', 'montant', 'livraison'])
                ->where('id', '=', $id);
            if($params === 'items'){
                $commande = $commande->with('items:id,libelle,tarif,quantite,command_id');
            }    
            $commande = $commande->firstOrFail();


            //complete the data array with datas who are gonna be returned in JSON format
            $data = [
                "type" => "resource",
                "commande" => $commande,
                "links" => [
                    "items" => ["href" => $url_itemsOfCommand ],
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
            $this->c->logger_debug->debug('GET / : debug (c\'est pas très grave, pas de gros soucis pour l\'instant');
            $this->c->logger_warning->warning('GET / : warning (au secours, tout va mal chef !');
            return JsonError::jsonError($req, $resp, 'error', 404,'Ressource not found : command ID = ' . $id );   

        }
    }

    // TD 3
    public function replaceCommand(Request $req, Response $resp, array $args): Response {
        //get id of the command un the URI
        $id = $args['id'];

        //get the body of the request
        $command_data = $req->getParsedBody();
            
            try {
            //get the command to replace
            $commandToReplace = Commande::findOrFail($id);

            //check if a data is missing
            if(!isset($command_data['nom_client'])){
                return JsonError::jsonError($req, $resp, 'error', 400, 'Missing data : nom_client' );
            }
            if(!isset($command_data['mail_client'])){
                return JsonError::jsonError($req, $resp, 'error', 400, 'Missing data : mail_client' );
            }
            if(!isset($command_data['date_livraison'])){
                return JsonError::jsonError($req, $resp, 'error', 400, 'Missing data : date_livraison' );
            }
            if(!isset($command_data['heure_livraison'])){
                return JsonError::jsonError($req, $resp, 'error', 400, 'Missing data : heure_livraison' );
            }

            //Sanitize datas
            $command_data['nom_client'] = filter_var($command_data['nom_client'], FILTER_SANITIZE_STRING);
            $command_data['mail_client']= filter_var($command_data['mail_client'], FILTER_SANITIZE_EMAIL);
            $livraison = new DateTime($command_data['date_livraison'] .' '. $command_data['heure_livraison']);
            $date_livraison = $livraison->format('Y-m-d H:i:s');
            
            //replace values with new values
            $commandToReplace->nom = $command_data['nom_client'];
            $commandToReplace->mail = $command_data['mail_client'];
            $commandToReplace->livraison = $date_livraison;
            //update the command
            $commandToReplace->save();

            //configure the response headers
            $resp = $resp->withStatus(204)
                ->withHeader('Content-Type', 'application/json; charset=utf-8');
            
            //return the response (ALWAYS !)
            return $resp;
            
        } catch (ModelNotFoundException $e) {
            return JsonError::jsonError($req, $resp, 'error', 404,'Ressource not found : command ID = ' . $id );
        }
        
        catch (\Exception $th) {
            return JsonError::jsonError($req, $resp, 'error', 500,'A exception is thrown : something is wrong with the update of datas' ); 
        }
    }

    // TD 4
    public static function getItemsOfCommand(Request $req, Response $resp, array $args): Response{
        $id = $args['id'];
        try {
            //get all the commands
        $commande = Commande::findOrFail($id);
        $items = $commande->items()
                ->select(['id', 'libelle', 'tarif', 'quantite'])
                ->get();

        //complete the data array with datas who are gonna be returned in JSON format
        $data = [
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
    
    // TD 5.1
    public static function createCommand(Request $req, Response $resp, array $args): Response{
        try {
            //get datas from the request
            $command_data = $req->getParsedBody();

            //get the uuid commande in the middleware createID
            $uuid_commande = $req->getAttribute('idCommande');

            //get the token in the middleware createToken
            $token_commande = $req->getAttribute('token') ;

            // $url_oneCommand = $this->c->router->pathFor('command', ['id'=>$uuid_commande]);

            $data = [
                "commande" => [
                    'nom'=> $command_data['nom'],
                    'mail'=> $command_data['mail'],
                    'date_livraison'=> $command_data['livraison']['date'] . ' ' . $command_data['livraison']['heure'],
                    'id' => $uuid_commande,
                    'token' => $token_commande,
                    'montant' => 0,
                ],
            ];

            $new_command = new Commande();
            // $new_command->id = $uuid_commande;
            // $new_command->livraison = $command_data['livraison']['date'] . ' ' . $command_data['livraison']['heure'];
            // $new_command->nom = $command_data['nom'];
            // $new_command->mail = $command_data['mail'];
            // $new_command->montant = 0;
            // $new_command->token = $token_commande;
            $new_command->save();


             //configure the response headers
            $resp = $resp->withStatus(201)
                        ->withHeader('Content-Type', 'application/json; charset=utf-8');
                        // ->withHeader('Location', '$url_oneCommand');

            //write in the body with data encode with a json_encode function
            $resp->getBody()->write(json_encode($data));

            //return the response (ALWAYS !)
            return $resp;

        } catch (ModelNotFoundException $e) {
            return JsonError::jsonError($req, $resp, 'error', 404,'Ressource not found : command ID = ' . $id );
        }
        catch (\Exception $th) {
            return JsonError::jsonError($req, $resp, 'error', 500,'A exception is thrown : something is wrong with the update of datas' ); 
        }


        
        
    }


}

//Command lines for container m2dhtml
// $md2html = $this->c->md2html;
// $md2html('# Hello');