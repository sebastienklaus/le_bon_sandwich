<?php

namespace lbs\fab\app\model;

use \lbs\fab\app\model\Item as Item;


use \Psr\Http\Message\ServerRequestInterface as Request ;
use \Psr\Http\Message\ResponseInterface as Response ;
use Psr\Container\ContainerInterface;

class Commande extends \Illuminate\Database\Eloquent\Model {

    protected $table      = 'commande';  /* le nom de la table */
    protected $primaryKey = 'id';
    public    $timestamps = true;    /* si vrai la table doit contenir
                                        les deux colonnes updated_at,
                                        created_at */
    

    public $incrementing = false;      //pour primarykey, on annule l'auto_increment
    public $keyType='string';

    public function items()
    {
        return $this->hasMany(Item::class, 'command_id', 'id');
    }

}
