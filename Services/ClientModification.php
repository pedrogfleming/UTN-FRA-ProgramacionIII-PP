<?php
require_once("../Repositories/ClientRepository.php");
class ClientModification
{
    private $_clientRepository;

    public function __construct() {
        $this->_clientRepository = new ClientRepository();        
    }
    public function Update($clientDTO){
        require_once("../Models/Client.php");
        $targetClient = new Client($clientDTO->name, $clientDTO->lastName, $clientDTO->documentType, $clientDTO->documentNumber, $clientDTO->email, $clientDTO->clientType, $clientDTO->country, $clientDTO->city, $clientDTO->phoneNumber);
        $targetClient->setId($clientDTO->clientId);

        $clientModified = $this->_clientRepository->Update($targetClient);
        return $clientModified;
    }
}
