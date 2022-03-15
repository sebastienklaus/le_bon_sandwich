<?php

require_once  __DIR__ . '/../src/vendor/autoload.php';


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use GuzzleHttp\Client as Client;

// set new client(s)

$clientCommand = new Client([
    // Base URL : pour ensuite transmettre des requêtes relatives
    'base_uri' => 'http://api.commande.local',
    // options par défaut pour les requêtes
    'timeout' => 2.0,
    ]);
$clientAuth = new Client([
    // Base URL : pour ensuite transmettre des requêtes relatives
    'base_uri' => 'http://api.auth.local',
    // options par défaut pour les requêtes
    'timeout' => 2.0,
    ]);

// Set the differents routes
