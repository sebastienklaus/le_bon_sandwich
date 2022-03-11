<?php

require_once  __DIR__ . '/../src/vendor/autoload.php';


use GuzzleHttp\Client as Client;

use \lbs\backoffice\app\controller\LBSAuthController as LBSAuthController;
use \lbs\fab\app\controller\CommandController as CommandController;
use \lbs\fab\app\middleware\Middleware as Middleware;
use lbs\fab\app\validators\Validators as validators;
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


$client = new Client([
    // Base URL : pour ensuite transmettre des requêtes relatives
    'base_url' => 'http://api.auth.local',
    // options par défaut pour les requêtes
    'timeout' => 2.0,
    ]);

// Set the differents routes

$app->get('/hello[/]', LBSAuthController::class . ':hello')
    ->setName('hello');
    
$app->run();