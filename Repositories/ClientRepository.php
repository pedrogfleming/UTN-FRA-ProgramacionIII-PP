<?php
require_once("filesManager.php");
require_once("../Models/Client.php");
class ClientRepository
{
    private $_fileName;
    private $_fileManager;
    private $_base_id = "1000001";

    public function __construct()
    {
        $this->_fileName = '../hoteles.json';
        $this->_fileManager = new filesManager();
    }
    public function Create($c)
    {
        $clients = file_exists($this->_fileName) ? $this->_fileManager->ReadJSON($this->_fileName) : array();
        if (!empty($clients)) {
            $nextId = ClientRepository::GetNextId($clients);
            $c->SetId($nextId);

            array_push($clients, $c);
            if ($this->_fileManager->SaveJSON($this->_fileName, $clients)) {
                return $this->Get($nextId);
            }
        } else {
            $c->SetId($this->_base_id);
            $clients[0] = $c;
            if ($this->_fileManager->SaveJSON($this->_fileName, $clients)) {
                $ret = $this->Get($c->GetId());
                return $ret;
            } else {
                throw new Exception("Unable to register the client");
            }
        }
    }

    public function Get($id = null)
    {
        $notFound =  array();
        if (file_exists($this->_fileName)) {
            $allClients = Client::map($this->_fileManager->ReadJSON($this->_fileName));
            if (isset($id)) {
                $foundedClient[0] = $this->SearchById($allClients, $id);
                if ($foundedClient[0] !== false) {
                    return $foundedClient;
                } else {
                    return $notFound;
                }
            } else {
                return $allClients;
            }
        } else {
            return $notFound;
        }
    }

    public function Update($client)
    {
        $newListClientModified = ClientRepository::Arr_Update($this->Get(), $client);
        if ($newListClientModified !== false && count($newListClientModified) > 0) {
            if ($this->_fileManager->SaveJSON($this->_fileName, $newListClientModified)) {
                return $this->Get($client->getId());
            } else {
                throw new Exception("Unable to modify the client");
            }
        } else {
            throw new Exception("Client not found");
        }
    }

    public function Delete($clientId, $clientType)
    {
        $clients = $this->Get();
        $deleted = false;
        for ($i = 0; $i < count($clients); $i++) {
            if (
                $clients[$i]->getId() == $clientId &&
                $clients[$i]->getClientType() == $clientType
            ) {
                $clients[$i]->isDeleted = true;
                $deleted = true;
            }
        }
        if($deleted){
            if (!$this->_fileManager->SaveJSON($this->_fileName, $clients)) {
                throw new Exception("Error while saving deleted client operation");
            }
        }
        else{
            throw new Exception("Client id and type combination couldnt be found");
        }
        return $deleted;
    }

    private static function Arr_Update($clients, $client)
    {
        for ($i = 0; $i < count($clients); $i++) {
            if (
                $clients[$i]->getId() == $client->getId() &&
                $clients[$i]->getClientType() == $client->getClientType()
            ) {
                $clients[$i] = $client;
                return $clients;
            }
        }
        return false;
    }

    private static function GetNextId($arr)
    {
        if (!empty($arr)) {
            usort($arr, function ($a, $b) {
                return $b->id - $a->id;
            });
            $ret = $arr[0]->id;
            $ret++;
            $ret = strval($ret);
            return $ret;
        }
    }

    private function SearchById($arr, $needle)
    {
        foreach ($arr as $client) {
            if ($client->getId() == $needle) {
                return $client;
            }
        }
        return false;
    }

    public function ClientExist($c)
    {
        $clients = $this->Get();
        foreach ($clients as $client) {
            if (Client::AreEqual($client, $c)) {
                return true;
            }
        }
        return false;
    }
}
