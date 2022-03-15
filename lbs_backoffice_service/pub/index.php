<?php

require_once  __DIR__ . '/../src/vendor/autoload.php';


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \lbs\backoffice\app\controller\BackOfficeController as BackOfficeController;
use GuzzleHttp\Client as Client;

$settings = require_once __DIR__. '/../src/app/conf/settings.php';
$errors = require_once __DIR__. '/../src/app/conf/errors.php';
$dependencies= require_once __DIR__. '/../src/app/conf/dependencies.php';

$app_config = array_merge($settings, $errors, $dependencies);


$app = new \Slim\App(new \Slim\Container($app_config));

// Set the differents routes
 
$app->post('/auth[/]', BackOfficeController::class . ':authenticate')
    ->setName('authentification');

$app->get('/hello[/]', BackOfficeController::class . ':hello')
    ->setName('commands');
    
$app->run();