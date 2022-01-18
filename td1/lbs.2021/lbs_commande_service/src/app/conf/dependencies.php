<?php

return[

    $c['db'] = function ($c) {
        
    
        $capsule = new \Illuminate\Database\Capsule\Manager;
        $capsule->addConnection($this['settings']['dbfile']);
        $capsule->bootEloquent();
        $capsule->setAsGlobal();
    }
];
