<?php

require_once  __DIR__ . '/../src/vendor/autoload.php';


use GuzzleHttp\Client as Client;

use \lbs\backoffice\app\controller\LBSAuthController as LBSAuthController;
use \lbs\fab\app\controller\CommandController as CommandController;
use \lbs\fab\app\middleware\Middleware as Middleware;
use lbs\fab\app\validators\Validators as validators;
use \DavidePastore\Slim\Validation\Validation as Validation ;

$client = new Client([
    // Base URL : pour ensuite transmettre des requêtes relatives
    'base_uri' => 'http://api.commande.local',
    // options par défaut pour les requêtes
    'timeout' => 2.0,
    ]);

// Set the differents routes

$response = $client->get('/commands');
    
echo $response->getBody();