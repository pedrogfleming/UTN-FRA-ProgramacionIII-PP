<?php
require_once "../Repositories/ClientRepository.php";
class ClientQuery
{
    private $_clientRepository;
    public function __construct()
    {
        $this->_clientRepository = new ClientRepository();
    }

    // Partial data = Show only country, city, client phone number
    public function Get($id = null, $clientType = null, $partialData = false)
    {
        $result = new stdClass();
        $clients = $this->_clientRepository->Get($id);
        if (isset($id) && empty($clients)) {
            throw new Exception('Client not found: client id dont match any client number');
        }
        if (isset($clientType)) {
            $clients = array_filter($clients, function ($client) use ($clientType) {
                return $client->getClientType() == $clientType;
            });
            $clients = array_values($clients);
            if (empty($clients)) {
                throw new Exception('Client not found: combination of Id client and type doesnt match any registered client');
            }
        }
        if ($partialData) {
            $clients = array_map(function ($client) {
                $c = new stdClass();
                $c->country = $client->getCountry();
                $c->city = $client->getCity();
                $c->clientPhoneNumber = $client->getPhoneNumber();
                return $c;
            }, $clients);
        }
        $result->clients = $clients;
        return $result;
    }
}
