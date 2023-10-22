<?php
require_once("filesManager.php");
require_once("./Models/Client.php");
class ClientRepository
{
    private $_fileName;
    private $_fileManager;
    private $_base_id = "1000001";

    public function __construct()
    {
        $this->_fileName = "./hoteles.json";
        $this->_fileManager = new filesManager(); 
    }
    public function Create($c)
    {
        if (file_exists($this->_fileName)) {
            $clients = $this->_fileManager->ReadJSON($this->_fileName);
            if (!empty($clients)) {
                $nextId = ClientRepository::GetNextId($clients);
                $c->SetId($nextId);

                array_push($clients, $c);
                if ($this->_fileManager->SaveJSON($this->_fileName, $clients)) {
                    return $this->Get($nextId);
                }
            }
        } else {
            $c->SetId($this->_base_id);
            $clients[0] = $c;
            if($this->_fileManager->SaveJSON($this->_fileName, $clients)){
                $ret = $this->Get($c->GetId());
                return $ret;
            }
            else{
                throw new Exception("Unable to register the client");
            }
        }
    }

    public function Get($id = null)
    {
        if (file_exists($this->_fileName)) {
            $clients = Client::map($this->_fileManager->ReadJSON($this->_fileName));
            if (isset($id)) {
                return $this->SearchById($clients, $id);
            } else {
                return $clients;
            }
        } else {
            return array();
        }
    }

    private static function GetNextId($arr)
    {
        if (!empty($arr)) {
            //get the id of the last Client stored in the json and add 1 to increment the id of the new Client.
            usort($arr, function ($a, $b) {
                return $a->id < $b->id;
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
