<?php

require_once  __DIR__ . '/../src/vendor/autoload.php';

use \lbs\command\app\controller\CommandController as CommandController;
use \lbs\command\app\middleware\Middleware as Middleware;
use lbs\command\app\validators\Validators as validators;
use \DavidePastore\Slim\Validation\Validation as Validation ;

$settings = require_once __DIR__. '/../src/app/conf/settings.php';
$errors = require_once __DIR__. '/../src/app/conf/errors.php';
$dependencies= require_once __DIR__. '/../src/app/conf/dependencies.php';

$app_config = array_merge($settings, $errors, $dependencies);


$app = new \Slim\App(new \Slim\Container($app_config));

// Initiate DB connection with Eloquent
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($app_config['settings']['dbfile']);
$capsule->bootEloquent();
$capsule->setAsGlobal();

// Set the differents routes
$app->get('/commands[/]', CommandController::class . ':listCommands')
    ->setName('commands');

$app->get('/commands/{id}[/]', CommandController::class . ':oneCommand')
    ->setName('command')
    ->add(Middleware::class . ':checkToken');

$app->post('/commands[/]', CommandController::class . ':createCommand')
    ->setName('creationCommand')
    ->add(Middleware::class . ':createID')
    ->add(Middleware::class . ':createToken')
    ->add(new Validation( Validators::validators_createCommand()) );

$app->put('/commands/{id}[/]', CommandController::class . ':replaceCommand')->setName('replaceCommand');
    
$app->get('/commands/{id}/items[/]', CommandController::class . ':getItemsOfCommand')->setName('commandWithItems');
    
    
$app->run();