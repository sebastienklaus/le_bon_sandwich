<?php

namespace lbs\command\app\controller;

use \Psr\Http\Message\ServerRequestInterface as Request ;
use \Psr\Http\Message\ResponseInterface as Response ;

use \lbs\command\app\model\Commande as Commande;
use lbs\command\app\error\ErrorHandler as ErrorHandler;


class TD1CommandController {


    public function listCommands(Request $req, Response $resp, array $args) : Response {
        try {
            $commandes = Commande::select(['id', 'nom', 'mail', 'montant', 'livraison'])
                                        ->get();

                    $resp = $resp->withStatus(200)
                            ->withHeader('Content-Type', 'application/json; charset=utf-8');

                    $data = [
                        "type" => "collection",
                        "count" => count($commandes),
                        "commandes"=> $commandes
                    ];

                    $resp->getBody()->write( json_encode($data) ) ;

                    return $resp ;
        } catch (\Exception $th) {
                $c = $app->getContainer() ;
                $c[ 'notFoundHandler' ]= function( $c ) {
                    return ErrorHandler::notFound();
                };
    }
        
    }


    public function oneCommand(Request $req, Response $resp, array $args) : Response{
        $id = $args['id'];

            $commande = Commande::select(['id', 'nom', 'mail', 'montant', 'livraison'])
                                    ->where('id', '=', $id)
                                    ->firstOrFail();

            $data = [
                "type" => "resource",
                "commande"=> $commande
            ];

            $resp = $resp->withStatus(200)
                        ->withHeader('Content-Type', 'application/json; charset=utf-8');
            $resp->getBody()->write( json_encode($data) ) ;
            return $resp ;

    }
}
