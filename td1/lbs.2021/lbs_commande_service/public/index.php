<?php
/**
 * File:  index.php
 *
 */

require_once  __DIR__ . '/../src/vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request ;
use \Psr\Http\Message\ResponseInterface as Response ;
use \lbs\command\app\controller\TD1CommandController as TD1CommandController;


use \lbs\command\app\model\Commande as Commande;


$settings = require_once __DIR__. '/../src/app/conf/settings.php';
// $dependencies= require_once __DIR__. '/../src/app/conf/dependencies.php';
$errors = require_once __DIR__. '/../src/app/conf/errors.php';

$app_config = array_merge($settings, $errors);

$app = new \Slim\App(new \Slim\Container($app_config));

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($app_config['settings']['dbfile']);
$capsule->bootEloquent();
$capsule->setAsGlobal();


$app->get('/TD1/commands[/]', TD1CommandController::class . ':listCommands');
$app->get('/TD1/commands/{id}[/]', TD1CommandController::class . ':oneCommand');

$app->run();