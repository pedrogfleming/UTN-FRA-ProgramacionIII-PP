<?php
class ClientController
{
    public function Create($dto)
    {
        require_once("../Services/ClientRegistration.php");
        $clientRegistration = new ClientRegistration();
        try {
            return $clientRegistration->RegisterClient($dto);
        } catch (\Throwable $th) {
            $response = new stdClass();
            $response->err = $th->getMessage();
            return $response;
        }
    }

    public function Get($request, $response, $args)
    {
        require_once("../Services/ClientQuery.php");
        try {
            $queryParams = $request->getQueryParams();
            if (
                isset($queryParams['clientId']) &&
                isset($queryParams["clientType"])
            ) {
                $dto = new stdClass();
                $dto->clientId = $queryParams["clientId"];
                $dto->clientType = $queryParams["clientType"];

                $clientQuery = new ClientQuery();
                $result = $clientQuery->Get($dto->clientId, $dto->clientType, true);
                $payload = json_encode(array($result));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }
            else{
                throw new Exception("Missing arguments on request");
            }
        } catch (\Throwable $th) {
            $payload = json_encode(array("err" => $th->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public function Update($dto)
    {
        require_once("../Services/ClientModification.php");
        try {
            $clientModification = new ClientModification();
            return $clientModification->Update($dto);
        } catch (\Throwable $th) {
            $response = new stdClass();
            $response->err = $th->getMessage();
            return $response;
        }
    }

    public function Delete($dto)
    {
        require_once("../Services/ClientDelete.php");
        $clientQuery = new ClientDelete();
        try {
            return $clientQuery->Delete($dto);
        } catch (\Throwable $th) {
            $response = new stdClass();
            $response->err = $th->getMessage();
            return $response;
        }
    }
}
