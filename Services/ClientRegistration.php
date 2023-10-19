<?php
require_once("./Repositories/ClientRepository.php");
    class ClientRegistration
    {
        private $_clientRepository;
        public function __construct() {            
            $this->_clientRepository = new ClientRepository();
        }
        function RegisterClient($clientDTO){        
            require_once("./Models/Client.php");
            if (isset($clientDTO)) {
                
    
                $newClient = new Client($clientDTO->name, $clientDTO->lastName, $clientDTO->documentType, $clientDTO->documentNumber, $clientDTO->email, $clientDTO->clientType, $clientDTO->country, $clientDTO->city, $clientDTO->phoneNumber);
                if(!$this->ClientExist($newClient)){
                    return  $this->_clientRepository->Create($newClient);
                }
                else{
                    throw new Exception('Unable to register the new client: client already exist');
                }
            }
        }
        private function ClientExist($c){
            $clients = $this->_clientRepository->Get();
            foreach ($clients as $client) {
                if (Client::AreEqual($client, $c)) {
                    return true;
                }
            }
            return false;
        }
    }
?>