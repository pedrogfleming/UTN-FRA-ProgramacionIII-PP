<?php
require_once '../Models/User.php';
require_once '../Utils/JWTAuthenticator.php';

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class TransactionLoggerMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $userData = JWTAuthenticator::GetData($token)->data;

        $requestData = [
            'datetime' => date('Y-m-d H:i:s'),
            'username' => $userData->username,
            'body' => $request->getParsedBody(),
            'queryParams' => $request->getQueryParams(),
        ];
        $response = $handler->handle($request);
        $responseData = (string) $response->getBody();
        $this->logTransaction($requestData, $responseData);
        return $response;
    }

    private function logTransaction($requestData, $responseData)
    {
        $objDAO = DAO::GetInstance();
        $command = $objDAO->prepareQuery("INSERT INTO Transactions (datetime, username, requestBody, requestQueryParams, responseBody) VALUES (?, ?, ?, ?, ?)");
        $command->execute([
            $requestData['datetime'],
            $requestData['username'],
            json_encode($requestData['body']),
            json_encode($requestData['queryParams']),
            $responseData,
        ]);
    }
}
