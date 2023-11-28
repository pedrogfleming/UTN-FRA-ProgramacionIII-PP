<?php
require_once("filesManager.php");
require_once("../Models/Client.php");
require_once "../database/dao.php";
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
        try {
            $objDAO = DAO::GetInstance();
            $command = $objDAO->prepareQuery("INSERT INTO Clients (name, lastName, documentType, documentNumber, email, clientType, country, city, phoneNumber, paymentMethod) VALUES (?,?,?,?,?,?,?,?,?,?)");
            $command->execute([$c->getName(), $c->getLastName(), $c->getDocumentType(), $c->getDocumentNumber(), $c->getEmail(), $c->getClientType(), $c->getCountry(), $c->getCity(), $c->getPhoneNumber(), $c->getPaymentMethod()]);
            return $objDAO->getLastId();
        } catch (PDOException $e) {
            throw new Exception("Unable to register the client: " . $e->getMessage());
        }
    }

    public function Get($id = null)
    {
        try {
            $objDAO = DAO::GetInstance();
            if (isset($id)) {
                $command = $objDAO->prepareQuery("SELECT * FROM Clients WHERE id = ? AND isDeleted = 0");
                $command->execute([$id]);
                $result = $command->fetch(PDO::FETCH_OBJ);
                if ($result) {
                    return $result;
                } else {
                    throw new Exception("Client not found");
                }
            } else {
                $command = $objDAO->prepareQuery("SELECT * FROM Clients WHERE isDeleted = 0");
                $command->execute();
                $results = $command->fetchAll(PDO::FETCH_OBJ);
                return $results;
            }
        } catch (PDOException $e) {
            throw new Exception("Unable to retrieve the client(s): " . $e->getMessage());
        }
    }

    public function Update($client)
    {
        try {
            $objDAO = DAO::GetInstance();
            $command = $objDAO->prepareQuery("UPDATE Clients SET name = ?, lastName = ?, documentType = ?, documentNumber = ?, email = ?, clientType = ?, country = ?, city = ?, phoneNumber = ?, paymentMethod = ? WHERE id = ? AND isDeleted = 0");
            $command->execute([$client->getName(), $client->getLastName(), $client->getDocumentType(), $client->getDocumentNumber(), $client->getEmail(), $client->getClientType(), $client->getCountry(), $client->getCity(), $client->getPhoneNumber(), $client->getPaymentMethod(), $client->getId()]);
            if ($command->rowCount() > 0) {
                return $this->Get($client->getId());
            } else {
                throw new Exception("Unable to modify the client or client not found");
            }
        } catch (PDOException $e) {
            throw new Exception("Unable to modify the client: " . $e->getMessage());
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
        if ($deleted) {
            if (!$this->_fileManager->SaveJSON($this->_fileName, $clients)) {
                throw new Exception("Error while saving deleted client operation");
            }
        } else {
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
        $clients = Client::map($this->Get());
        foreach ($clients as $client) {
            if (Client::AreEqual($client, $c)) {
                return true;
            }
        }
        return false;
    }
}
