<?php
require_once '../Utils/JWTAuthenticator.php';

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AuthenticationMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        try {
            JWTAuthenticator::VerifyToken($token);
            return $handler->handle($request);
        } catch (Exception $e) {
            $message = "";
            if($e->getCode() == 0){
                $message = "Could not authenticate the request: " . $e->getMessage();
            }
            else{
                $message = "Error: " . $e->getMessage();
            }
            $response = new Response();
            $payload = json_encode(['message' => $message]);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }
}
