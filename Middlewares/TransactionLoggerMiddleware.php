<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class TransactionLoggerMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        // Get the request data
        $requestData = [
            'datetime' => date('Y-m-d H:i:s'),
            'user' => $this->getAuthenticatedUserId(),
            'body' => $request->getParsedBody(),
            'queryParams' => $request->getQueryParams(),
        ];
        $response = $handler->handle($request);
        $responseData = (string) $response->getBody();
        $this->logTransaction($requestData, $responseData);
        return $response;
    }

    private function getAuthenticatedUserId()
    {
        // Implement this method to return the authenticated user's ID
    }

    private function logTransaction($requestData, $responseData)
    {
        $objDAO = DAO::GetInstance();
        $command = $objDAO->prepareQuery("INSERT INTO Transactions (datetime, user, requestBody, requestQueryParams, responseBody) VALUES (?, ?, ?, ?, ?)");
        $command->execute([
            $requestData['datetime'],
            $requestData['user'],
            json_encode($requestData['body']),
            json_encode($requestData['queryParams']),
            $responseData,
        ]);
    }
}
