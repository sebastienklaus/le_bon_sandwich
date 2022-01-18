<?php

namespace lbs\command\app\controller;

use \Psr\Http\Message\ServerRequestInterface as Request ;
use \Psr\Http\Message\ResponseInterface as Response ;
use Psr\Container\ContainerInterface;

use \lbs\command\app\model\Commande as Commande;


class TD1CommandController {

    // constructor receives container instance
    // public function __construct(ContainerInterface $container) {
    //     $this->container = $container;
    // }


    public $commands = [
        ["id" => "45RF56TH", "mail_client" => "g@g.fr", "date_commande" => "2021-12-01", "montant"=>50.0],
        ["id" => "46RF56TH", "mail_client" => "a@aaa.fr", "date_commande" => "2022-01-16", "montant"=>45.0],
        ["id" => "47RF56TH", "mail_client" => "l@ll.fr", "date_commande" => "2022-01-18", "montant"=>27.5],
        ["id" => "48RF56TH", "mail_client" => "m@mm.fr", "date_commande" => "2022-01-19", "montant"=>30.0],
    ];

    public function listCommands(Request $req, Response $resp, array $args) : Response {
        
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
        
    }


    public function oneCommand(Request $req, Response $resp, array $args) : Response{
        $id = $args['id'];

        $commande = Commande::select(['id', 'nom', 'mail', 'montant', 'livraison'])
                                ->where('id', '=', $id)
                                ->firstOrFail();

        $resp = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json; charset=utf-8');

        $data = [
            "type" => "resource",
            "commande"=> $commande
        ];

        $resp->getBody()->write( json_encode($data) ) ;

        return $resp ;

    }
}
