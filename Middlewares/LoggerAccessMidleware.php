<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AccessLoggerMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);

        $resource = $request->getUri()->getPath();
        $userId = $this->getAuthenticatedUserId();

        // Log the access
        $this->logAccess($resource, $userId);

        return $response;
    }

    private function getAuthenticatedUserId()
    {
        // Implement this method to return the authenticated user's ID
    }

    private function logAccess($resource, $userId)
    {
        require_once "../database/dao.php";
        $objDAO = DAO::GetInstance();
        $command = $objDAO->prepareQuery("INSERT INTO AccessLogs (resource, userId) VALUES (?, ?)");
        $command->execute([$resource, $userId]);
    }
}
