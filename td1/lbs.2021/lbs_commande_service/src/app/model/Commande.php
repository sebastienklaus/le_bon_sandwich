<?php

namespace lbs\command\app\model;

use \Psr\Http\Message\ServerRequestInterface as Request ;
use \Psr\Http\Message\ResponseInterface as Response ;
use Psr\Container\ContainerInterface;

class Commande extends \Illuminate\Database\Eloquent\Model {

    protected $table      = 'commande';  /* le nom de la table */
    protected $primaryKey = 'id';

    public  $incrementing = false;      //pour primarykey, on annule l'auto_increment
    public $keyType='string';

}
