<?php

return[

    'logger_debug' => function(\Slim\Container $c ) {
        $log= new \Monolog\Logger($c->settings['debug.name']) ;
        $log->pushHandler( new \Monolog\Handler\StreamHandler($c->settings['debug.log'],$c->settings['debug.level']) ) ;
        return $log ;
    },
    'logger_warning' => function(\Slim\Container $c ) {
        $log= new \Monolog\Logger($c->settings['warning.name']) ;
        $log->pushHandler( new \Monolog\Handler\StreamHandler($c->settings['warning.log'],$c->settings['warning.level']) ) ;
        return $log ;
    },

];
