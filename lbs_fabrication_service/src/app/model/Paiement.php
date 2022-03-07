<?php

namespace lbs\fab\app\model;

use \Psr\Http\Message\ServerRequestInterface as Request ;
use \Psr\Http\Message\ResponseInterface as Response ;
use Psr\Container\ContainerInterface;

class Paiement extends \Illuminate\Database\Eloquent\Model {

    protected $table      = 'paiement';  /* le nom de la table */


    public  $incrementing = false;

}
