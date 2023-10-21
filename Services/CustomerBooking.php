<?php
require_once("./Repositories/ClientRepository.php");
class CustomerBooking
{
    private $_clientRepository;
    public function __construct()
    {
        $this->_clientRepository = new ClientRepository();
    }
    public function Get($clientDTO)
    {
        require_once("./Models/Client.php");
        $ret = new stdClass();
        $err_message = null;
        $clients = $this->_clientRepository->Get();

        $ids = array_filter($clients, function ($obj) use ($clientDTO) {
            return $obj->getId() == $clientDTO->id;
        });
        
        if (count($ids) > 0) {
            $foundedClient = array_filter($ids, function ($obj) use ($clientDTO) {
                return $obj->getClientType() == $clientDTO->clientType;
            });
        
            if (count($foundedClient) > 0) {
                $foundedClient = array_values($foundedClient);
                $ret = (object) array(
                    'country' => $foundedClient[0]->getCountry(),
                    'city' => $foundedClient[0]->getCity(),
                    'phone' => $foundedClient[0]->getPhoneNumber()
                );
                return $ret;
            } else {
                $ret->err = "Client type incorrect";
            }
        } else {
            $ret->err = "Number and type of client are incorrect";
        }
        return $ret;
    }
}
