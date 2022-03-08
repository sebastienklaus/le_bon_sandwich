<?php

namespace lbs\fab\app\middleware;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use lbs\fab\app\error\JsonError as JsonError;
use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;

use Ramsey\Uuid\Uuid;
use \DavidePastore\Slim\Validation\Validation as Validation ;

class Middleware {

    private $container; // le conteneur de dépendences de l'application

    public function __construct(\Slim\Container $container)
    {
        $this->container = $container;
    }

    // * création d'un ID avec un UUID (utilisation de la librairie Ramsey\UUID)
    public static function createID(Request $req, Response $resp, callable $next){
        // création d'un ID avec la librairie UUID
        $uuid4_id = Uuid::uuid4();
        // creation de l'attribut "idCommande"
        $req = $req->withAttribute('idCommande', $uuid4_id->toString());

        $resp = $next($req,$resp);

        return $resp;
    }
    
    // * création d'un token avec random_bytes()
    public static function createToken(Request $req, Response $resp, callable $next){
        $token = random_bytes(32);
        $token = bin2hex($token);
        // creation de l'attribut "token"
        $req = $req->withAttribute('token', $token);

        $resp = $next($req,$resp);

        return $resp;
    }

    // * check d'un token pour afficher ou non une commande
    public static function checkToken(Request $req, Response $resp, callable $next){
        $queryParams = $req->getQueryParams();
        
        if (isset($queryParams['token'])){
            $token_uri = $req->getQueryParam('token' , null);
            $req = $req->withAttribute( 'token' , $token_uri );
        }
        else {
            $token_header = $req->getHeader('X-lbs-token');
            $req = $req->withAttribute( 'token' , $token_header );
        }

        $resp = $next($req,$resp);

        return $resp;
    }

}