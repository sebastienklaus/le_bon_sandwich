<?php

use lbs\command\app\error\ErrorHandler as ErrorHandler;

return [
    //erreur 400
    'notFoundHandler' => function( $c ) {
            return ErrorHandler::notFound();
    },

    //error 404
    'errorHandler' => function( $c ) {
        return ErrorHandler::errorHandler();
    },


];