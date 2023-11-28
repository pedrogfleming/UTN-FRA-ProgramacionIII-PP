<?php
require_once("filesManager.php");
require_once("../Models/Client.php");
require_once "../database/dao.php";
class ClientRepository
{
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
        try {
            $objDAO = DAO::GetInstance();
            $command = $objDAO->prepareQuery("UPDATE Clients SET isDeleted = 1 WHERE id = ? AND clientType = ? AND isDeleted = 0");
            $command->execute([$clientId, $clientType]);
            if ($command->rowCount() > 0) {
                return true;
            } else {
                throw new Exception("Client id and type combination couldn't be found or client is already deleted");
            }
        } catch (PDOException $e) {
            throw new Exception("Error while performing delete operation: " . $e->getMessage());
        }
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
