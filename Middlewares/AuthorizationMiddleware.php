<?php
require_once '../Models/User.php';
require_once '../Utils/JWTAuthenticator.php';
require_once '../Services/UserRegistration.php';
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\Response;


class AuthorizationMiddleware
{
    private $authorizedRoles;

    public function __construct(array $authorizedRoles)
    {
        $this->authorizedRoles = $authorizedRoles;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $headerParams = $request->getServerParams();

        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $userData = JWTAuthenticator::GetData($token);

        $authorization = $this->isAuthorized($userData->data);
        if ($authorization->isAuthorized) {
            return $handler->handle($request);
        } else {
            $response = new Response();
            $payload = json_encode(['message' => $authorization->msg]);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    private function isAuthorized($userData)
    {
        $ret = new stdClass();
        $ret->isAuthorized = true;
        $ret->msg = "User authenticated successfully";

        if ($userData->role) {
            $authorizedRole = in_array($userData->role, $this->authorizedRoles);
            if (!$authorizedRole) {
                $ret->isAuthorized = false;
                $ret->msg = "User does not have sufficient permissions to perform the action";
            }
        } else {
            $ret->isAuthorized = false;
            $ret->msg = "User does not exist";
        }
        return $ret;
    }
}
