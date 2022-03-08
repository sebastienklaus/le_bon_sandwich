<?php

namespace lbs\fab\app\model;

use \Psr\Http\Message\ServerRequestInterface as Request ;
use \Psr\Http\Message\ResponseInterface as Response ;
use Psr\Container\ContainerInterface;

class Item extends \Illuminate\Database\Eloquent\Model {

    protected $table      = 'item';  /* le nom de la table */
    protected $primaryKey = 'id';

    public $timestamps = false;

}
