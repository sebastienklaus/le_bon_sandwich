<?php

return [
    'settings' => [
        'displayErrorDetails' => true,

        //DEBUG LOG
        'debug.name' => 'slim.log',
        'debug.log' => __DIR__ . '/../log/debug.log',
        'debug.level' => \Monolog\Logger::DEBUG,
        
        //WARNING LOG
        'warning.name' => 'slim.log',
        'warning.log' => __DIR__ . '/../log/warning.log',
        'warning.level' => \Monolog\Logger::WARNING,

        //client(s) Guzzle
        'auth_service' => 'http://api.auth.local',
        'command_service' => 'http://api.commande.local',
    ]
];