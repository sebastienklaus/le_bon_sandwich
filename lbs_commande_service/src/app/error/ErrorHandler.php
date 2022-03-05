<?php

namespace lbs\command\app\error;

use \Psr\Http\Message\ServerRequestInterface as Request ;
use \Psr\Http\Message\ResponseInterface as Response ;
use Psr\Container\ContainerInterface;


class ErrorHandler extends \Exception{

    static public function errorHandler(){
            return function( $req, $resp ) {
                $error = 400;
                $msg = "Bad Request - URI non traitée";
    
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


    static public function notFound(){
        return function( $req, $resp ) {
            $uri = $req->getUri();
            $error = 404;
            $msg = "Ressource non disponible : $uri";

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


    static public function notAllowedHandler(){
        return function( $req, $resp, $methods ) {
            $error = 405;

            $resp= $resp ->withStatus( $error )
                         ->withHeader('Content-Type', 'application/json; charset=utf-8')
                         ->withHeader('Allow', implode(',', $methods) );
            $resp->getBody()->write( json_encode('Méthode(s) permises : ' . implode(',', $methods) )) ;
            return $resp ;
        };
    }


    static public function phpErrorHandler(){
        return function( $req, $resp, $e ) {
            $error = 500;
            
            $resp= $resp->withStatus( $error ) ;
            $resp->getBody()
                    ->write( 'error :' . $e->getMessage() . ' in file : ' . $e->getFile() . ' line : ' . $e->getLine());
            return $resp ;
        };
    }




}