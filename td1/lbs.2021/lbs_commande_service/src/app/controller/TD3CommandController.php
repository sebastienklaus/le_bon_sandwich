<?php

namespace lbs\command\app\controller;

use DateTime;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \lbs\command\app\model\Commande as Commande;
use lbs\command\app\error\JsonError as JsonError;
use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;


class TD3CommandController{

    private $c; // le conteneur de dépendences de l'application

    public function __construct(\Slim\Container $c)
    {
        $this->c = $c;
    }


    public function replaceCommand(Request $req, Response $resp, array $args): Response {
        //get id of the command un the URI
        $id = $args['id'];

        //get the body of the request
        $command_data = $req->getParsedBody();
        
        // if(!isset($command_data['nom_client'])){
            //     //return error in json with 400 error code with msg 'missing data'
            // }
            
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
}


//Charles.Lombard@wanadoo.fr
//Charles.Lombard
//2021-05-29 15:17:53