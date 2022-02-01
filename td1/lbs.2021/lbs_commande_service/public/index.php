<?php

require_once  __DIR__ . '/../src/vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request ;
use \Psr\Http\Message\ResponseInterface as Response ;
use \lbs\command\app\controller\CommandController as CommandController;


use \lbs\command\app\model\Commande as Commande;


$settings = require_once __DIR__. '/../src/app/conf/settings.php';
$errors = require_once __DIR__. '/../src/app/conf/errors.php';
$dependencies= require_once __DIR__. '/../src/app/conf/dependencies.php';

$app_config = array_merge($settings, $errors, $dependencies);


$app = new \Slim\App(new \Slim\Container($app_config));

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($app_config['settings']['dbfile']);
$capsule->bootEloquent();
$capsule->setAsGlobal();

// print_r(__DIR__. '/../src/app/conf/dependencies.php');
$app->get('/commands/{id}/items[/]', CommandController::class . ':getItemsOfCommand')->setName('commandWithItems');

$app->get('/commands/{id}[/]', CommandController::class . ':oneCommand')->setName('command');
$app->put('/commands/{id}[/]', CommandController::class . ':replaceCommand')->setName('replaceCommand');

$app->get('/commands[/]', CommandController::class . ':listCommands')->setName('commands');


$app->run();