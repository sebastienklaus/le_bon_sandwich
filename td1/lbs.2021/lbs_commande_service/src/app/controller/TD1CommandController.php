<?php

namespace lbs\command\app\controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \lbs\command\app\model\Commande as Commande;
use \lbs\command\app\error\ErrorHandler as ErrorHandler;

use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;


class TD1CommandController
{

    private $c; // le conteneur de dépendences de l'application

    public function __construct(\Slim\Container $c)
    {
        $this->c = $c;
    }


    public function listCommands(Request $req, Response $resp, array $args): Response
    {
        $commandes = Commande::select(['id', 'nom', 'mail', 'montant', 'livraison'])
            ->get();

        $resp = $resp->withStatus(200)
            ->withHeader('Content-Type', 'application/json; charset=utf-8');

        $data = [
            "type" => "collection",
            "count" => count($commandes),
            "commandes" => $commandes
        ];

        $resp->getBody()->write(json_encode($data));

        return $resp;
    }


    public function oneCommand(Request $req, Response $resp, array $args): Response
    {
        $id = $args['id'];

        try {
            $commande = Commande::select(['id', 'nom', 'mail', 'montant', 'livraison'])
                ->where('id', '=', $id)
                ->firstOrFail();

            $data = [
                "type" => "resource",
                "commande" => $commande
            ];

            $resp = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json; charset=utf-8');
            $resp->getBody()->write(json_encode($data));
            return $resp;
        } catch (ModelNotFoundException $e) {
            //remplacer par un retour de message en JSON etc
            $data = [
                'type' => 'error',
                'error' => 404,
                'message' => "Ressource not found : command ID = " . $id
            ];
            
            $resp = $resp->withStatus(404)
                        ->withHeader('Content-Type', 'application/json; charset=utf-8');
            $resp->getBody()->write(json_encode($data));
            return $resp;
        }
    }
}
