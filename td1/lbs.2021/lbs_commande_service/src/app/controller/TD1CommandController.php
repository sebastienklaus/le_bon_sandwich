<?php

namespace lbs\command\app\controller;

use \Psr\Http\Message\ServerRequestInterface as Request ;
use \Psr\Http\Message\ResponseInterface as Response ;
use Psr\Container\ContainerInterface;

class TD1CommandController {

    // constructor receives container instance
//    public function __construct(ContainerInterface $container) {
//     $this->container = $container;
//     }


    public $commands = [
        ["id" => "45RF56TH", "mail_client" => "g@g.fr", "date_commande" => "2021-12-01", "montant"=>50.0],
        ["id" => "46RF56TH", "mail_client" => "a@aaa.fr", "date_commande" => "2022-01-16", "montant"=>45.0],
        ["id" => "47RF56TH", "mail_client" => "l@ll.fr", "date_commande" => "2022-01-18", "montant"=>27.5],
        ["id" => "48RF56TH", "mail_client" => "m@mm.fr", "date_commande" => "2022-01-19", "montant"=>30.0],
    ];

    public function listCommands(Request $req, Response $resp, array $args) : Response {
        
        $data = [
            "type" => "collection",
            "count" => count($this->commands),
            "commandes"=> $this->commands
        ];
        
        $resp->getBody()->write( json_encode( $data ) ) ;
        return $resp ;
    }
}
