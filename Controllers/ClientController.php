<?php
class ClientController
{
    public function Create($dto)
    {
        require_once("./Services/ClientRegistration.php");
        $clientRegistration = new ClientRegistration();
        try {
            return $clientRegistration->RegisterClient($dto);
        } catch (\Throwable $th) {
            $response = new stdClass();
            $response->err = $th->getMessage();
            return $response;
        }
    }

    public function Get($dto)
    {
        require_once("./Services/ClientQuery.php");
        $clientQuery = new ClientQuery();
        try {
            return $clientQuery->Get($dto->clientId, $dto->clientType, true);
        } catch (\Throwable $th) {
            $response = new stdClass();
            $response->err = $th->getMessage();
            return $response;
        }
    }

    public function Update($dto)
    {
        require_once("./Services/ClientModification.php");
        try {
            $clientModification = new ClientModification();
            return $clientModification->Update($dto);
        } catch (\Throwable $th) {
            $response = new stdClass();
            $response->err = $th->getMessage();
            return $response;
        }
    }
}
