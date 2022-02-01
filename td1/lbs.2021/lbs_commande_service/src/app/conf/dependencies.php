<?php

return[

    'logger' => function(\Slim\Container $c ) {
        $log= new \Monolog\Logger($c->settings['log.name']) ;
        $log->pushHandler( new \Monolog\Handler\StreamHandler($c->settings['debug.log'],$c->settings['log.level']) ) ;
        return $log ;
    },

];
