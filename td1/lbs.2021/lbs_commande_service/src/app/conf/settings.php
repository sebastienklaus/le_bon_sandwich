<?php

return [
    'settings' => [
        'displayErrorDetails' => true,
        'dbfile' => parse_ini_file('commande.db.conf.ini.dist'),
        'debug.log' => __DIR__ . '/../log/debug.log',
        'log.level' => \Monolog\Logger::DEBUG,
        'log.name' => 'slim.log'
    ]
];