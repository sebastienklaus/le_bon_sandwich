<?php
/**
 * File:  index.php
 *
 */

require_once  __DIR__ . '/../src/vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request ;
use \Psr\Http\Message\ResponseInterface as Response ;
use \lbs\command\app\controller\TD1CommandController as TD1CommandController;

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ]
];
$c = new \Slim\Container($configuration);
$app = new \Slim\App($c);

// récupérer le conteneur :

// $container = $app->getContainer() ;
// $prod = $container['settings']['displayErrorDetails'] ;

$app->get('/TD1/commands[/]', TD1CommandController::class . ':listCommands');
$app->get('/TD1/commands/{id}[/]', TD1CommandController::class . ':oneCommand');

$app->run();