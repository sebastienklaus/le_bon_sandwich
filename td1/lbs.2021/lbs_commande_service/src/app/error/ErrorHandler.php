<?php

namespace lbs\command\app\error;

use \Psr\Http\Message\ServerRequestInterface as Request ;
use \Psr\Http\Message\ResponseInterface as Response ;
use Psr\Container\ContainerInterface;


class ErrorHandler extends \Exception{

    static public function notFound(){
            return function( $req, $resp ) {
                $error = 404;
                $msg = "URI non traitée";
    
                $data = [
                    "type" => "error",
                    "error" => $error,
                    "msg"=> $msg,
                ];
    
    
                $resp = $resp->withStatus( $error )
                            ->withHeader('Content-Type', 'application/json; charset=utf-8');
                $resp->getBody()->write( json_encode($data) ) ;
                return $resp ;
            };
    }


    static public function errorHandler(){
        return function( $req, $resp ) {
            $uri = $req->getUri();
            $error = 400;
            $msg = "Bad Request | Ressource non disponible : $uri";

            $data = [
                "type" => "error",
                "error" => $error,
                "msg"=> $msg
            ];


            $resp= $resp->withStatus( $error )
                        ->withHeader('Content-Type', 'application/json; charset=utf-8');
            $resp->getBody()->write( json_encode($data) ) ;
            return $resp ;
        };
    }



}