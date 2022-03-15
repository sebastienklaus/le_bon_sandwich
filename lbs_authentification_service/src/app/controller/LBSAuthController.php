<?php

namespace lbs\auth\app\controller;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException ;
use Firebase\JWT\BeforeValidException;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\auth\app\model\User;
use lbs\auth\app\error\Writer as Writer;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


class LBSAuthController
{

    private $container; // le conteneur de dépendences de l'application
    

    public function __construct(\Slim\Container $container)
    {
        $this->container = $container;
    }


    public function authenticate(Request $req, Response $resp, $args): Response {

        if (!$req->hasHeader('Authorization')) {

            $resp = $resp->withHeader('WWW-authenticate', 'Basic realm="commande_api api" ');
            return Writer::jsonError($req, $resp, 'error', 401, 'No Authorization header present');
        };

        $authstring = base64_decode(explode(" ", $req->getHeader('Authorization')[0])[1]);
        list($email, $pass) = explode(':', $authstring);

        try {
            $user = User::select('id', 'email', 'username', 'passwd', 'refresh_token', 'level')
                ->where('email', '=', $email)
                ->firstOrFail();

            if (!password_verify($pass, $user->passwd))
                throw new \Exception("password check failed");

            unset ($user->passwd);

        } catch (ModelNotFoundException $e) {
            $resp = $resp->withHeader('WWW-authenticate', 'Basic realm="lbs auth" ');
            return Writer::jsonError($req, $resp, 'error', 401, 'Erreur authentification');
        } catch (\Exception $e) {
            $resp = $resp->withHeader('WWW-authenticate', 'Basic realm="lbs auth" ');
            return Writer::jsonError($req, $resp, 'error', 401, 'Erreur authentification');
        }


        $secret = $this->container->settings['secret'];
        $token = JWT::encode(['iss' => 'http://api.auth.local/auth',
            'aud' => 'http://api.fabrication.local',
            'iat' => time(),
            'exp' => time() + (12 * 30 * 24 * 3600),
            'upr' => [
                'email' => $user->email,
                'username' => $user->username,
                'level' => $user->level
            ]],
            $secret, 'HS512');

        $user->refresh_token = bin2hex(random_bytes(32));
        $user->save();
        $data = [
            'access-token' => $token,
            'refresh-token' => $user->refresh_token
        ];

        return Writer::json_output($resp, 200, $data);


    }

    public function check(Request $req, Response $resp, $args): Response {

        try {
            
            $secret = $this->container->settings['secret'];

            $h = $req->getHeader('Authorization')[0] ;
            $tokenstring = sscanf($h, "Bearer %s")[0] ;
            $token = JWT::decode($tokenstring, new Key($secret,'HS512' ) );

            $data = [
                'user_mail' => $token->upr->email,
                'user_username' => $token->upr->username,
                'user_level' => $token->upr->level,
            ];

            return Writer::json_output($resp, 200, $data);

        } 
        catch (ExpiredException $e) {
            return Writer::jsonError($req, $resp, 'error', 401, 'The token is expired');

        } catch (SignatureInvalidException $e) {
            return Writer::jsonError($req, $resp, 'error', 401, 'The signature is not valid');

        } catch (BeforeValidException $e) {
            return Writer::jsonError($req, $resp, 'error', 401, 'BeforeValidException');

        } catch (\UnexpectedValueException $e) { 
            return Writer::jsonError($req, $resp, 'error', 401, 'The value of token is not the right one');

        }    

        return $resp;
    }

}