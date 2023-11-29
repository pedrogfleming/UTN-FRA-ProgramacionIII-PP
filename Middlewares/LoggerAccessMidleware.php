<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
require_once '../Utils/JWTAuthenticator.php';
class AccessLoggerMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);

        $resource = $request->getUri()->getPath();
        
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $userData = JWTAuthenticator::GetData($token)->data;

        $this->logAccess($resource, $userData->username);

        return $response;
    }

    private function logAccess($resource, $username)
    {
        require_once "../database/dao.php";
        $objDAO = DAO::GetInstance();
        $command = $objDAO->prepareQuery("INSERT INTO AccessLogs (resource, username) VALUES (?, ?)");
        $command->execute([$resource, $username]);
    }
}
