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

    private $container; // le conteneur de dépendences de l'application

    public function __construct(\Slim\Container $container)
    {
        $this->container = $container;
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

    // TD 1 & 2 & 4 & 5.2
    public function oneCommand(Request $req, Response $resp, array $args): Response {
        //get the id in the URI with the args array
        $id = $args['id'];
        
        //try & ctach in case the id doesn't exist
        try {

            //get the actual URI & params
            $url_oneCommand = $this->container->router->pathFor('command', ['id'=>$id]);
            $url_itemsOfCommand = $this->container->router->pathFor('commandWithItems', ['id'=>$id]);
            $param_embed = $req->getQueryParam('embed' , null); 

            $param_token = $req->getAttribute('token');

            //get the command with some id
            $commande = Commande::select(['id', 'nom', 'created_at', 'livraison', 'mail', 'montant', 'livraison', 'token'])
                ->where('id', '=', $id)
                ->where('token', '=', $param_token);
            if($param_embed === 'items'){
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
            $this->container->logger_debug->debug('GET / : debug (c\'est pas très grave, pas de gros soucis pour l\'instant');
            $this->container->logger_warning->warning('GET / : warning (au secours, tout va mal chef !');
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
    public function createCommand(Request $req, Response $resp, array $args): Response{

        // initialisation du montant total de la commande
        $montant_total = 0.00;


        if ($req->getAttribute('has_errors')) {
            $errors = $req->getAttribute('errors');
            print_r($errors);
        } else {
            try {
                //get datas from the request
                $command_data = $req->getParsedBody();

                //get the uuid commande in the middleware createID
                $uuid_commande = $req->getAttribute('idCommande');

                //get the token in the middleware createToken
                $token_commande = $req->getAttribute('token') ;

                
                /** 
                 * Création de la commande avec le token + uuid
                 */
                // ! Création d'un DateTime pour insérer la date de livraison dans le modèle
                $date_livraison = new DateTime($command_data['livraison']['date'] .' '. $command_data['livraison']['heure']);
                
                $new_command = new Commande();
                //on filtre les données nom & mail
                $new_command->nom = filter_var($command_data['nom'], FILTER_SANITIZE_STRING);
                $new_command->mail = filter_var($command_data['mail'], FILTER_SANITIZE_EMAIL);
                $new_command->livraison = $date_livraison->format('Y-m-d H:i:s');
                // id = uuid crée par le middleware dédié
                $new_command->id = $uuid_commande;
                //token = token crée par le middleware dédié
                $new_command->token = $token_commande;  


                foreach ($command_data['items'] as $item ) {
                    // on crée un nouvel Item en définissant chacun de ses attributs
                    $new_item = new Item();
                    $new_item->uri = $item['uri'];
                    $new_item->quantite = $item['q'];
                    $new_item->libelle = $item['libelle'];
                    $new_item->tarif = $item['tarif'];
                    $new_item->command_id = $new_command->id;
                    $new_item->save();

                    //on augment le tarif total pour chaque tarif d'item
                    $montant_total += $item['tarif'];
                }
                //le montant total de la commande se réfère au montant calculé précedemment
                $new_command->montant = $montant_total;
                // on sauvegarde la nouvelle commande
                $new_command->save();

                // uri pour 1 commande (celle crée précédemment)
                $uri_getCommand = $this->container->router->pathFor('command', ['id'=>$new_command->id]);
                
                // initialisation du tableau de data dans le body de la réponse
                $data = [
                    "commande" => [
                        'nom'=> $new_command->nom,
                        'mail'=> $new_command->mail,
                        'date_livraison'=> $date_livraison->format('Y-m-d'),
                        'id' => $new_command->id,
                        'token' => $new_command->token,
                        'montant' => $new_command->montant,
                        // 'montant' => $uri_getCommand,
                    ],
                ];

                //configure the response headers
                $resp = $resp->withStatus(201)
                            ->withHeader('Content-Type', 'application/json; charset=utf-8')
                            ->withHeader('Location', $uri_getCommand);

                //write in the body with data encode with a json_encode function
                $resp->getBody()->write(json_encode($data));

                //return the response (ALWAYS !)
                return $resp;
            }
            catch (ModelNotFoundException $e) {
                return JsonError::jsonError($req, $resp, 'error', 404,'Ressource not found : command ID = ' . $uuid_commande );
            }
            catch (\Exception $th) {
                return JsonError::jsonError($req, $resp, 'error', 500,'A exception is thrown : something is wrong with the update of datas' ); 
            }
        }
        
    }


}

//Command lines for container m2dhtml
// $md2html = $this->c->md2html;
// $md2html('# Hello');