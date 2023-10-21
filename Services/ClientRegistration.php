<?php
require_once("./Repositories/ClientRepository.php");
class ClientRegistration
{
    private $_clientRepository;
    public function __construct()
    {
        $this->_clientRepository = new ClientRepository();
    }
    function RegisterClient($clientDTO)
    {
        require_once("./Models/Client.php");
        if (isset($clientDTO)) {
            $newClient = new Client($clientDTO->name, $clientDTO->lastName, $clientDTO->documentType, $clientDTO->documentNumber, $clientDTO->email, $clientDTO->clientType, $clientDTO->country, $clientDTO->city, $clientDTO->phoneNumber);
            if (!$this->ClientExist($newClient)) {
                $createdClient =  $this->_clientRepository->Create($newClient);
                if (!empty($createdClient)) {
                    $fileName = $createdClient->getId() . $createdClient->getClientType();
                    if ($this->UploadImage($fileName)->success) {
                        return $createdClient;
                    } else {
                        // TODO change exception for ret object with errors
                        throw new Exception('Unable to upload image for client');
                    }
                }
            } else {
                throw new Exception('Unable to register the new client: client already exist');
            }
        }
    }

    function UploadImage($file_name)
    {
        $ret = new stdClass;
        // The folder must be created before
        $file_folder = 'ImagenesDeClientes/2023/';

        // Data from the file sent by POST
        $file_type =  $_FILES['userImage']['type'];
        $file_size =  $_FILES['userImage']['size'];

        // Destination path, folder + name of the file I want to save
        $destination_path = $file_folder . $file_name;
        // We perform the validations of the file
        if (!((strpos($file_type, "png") || strpos($file_type, "jpeg")) && ($file_size < 100000))) {
            $ret->success = false;
            $ret->err = "The extension or the size of the files is not correct. <br><br><table><tr><td><li>.png or .jpg files are allowed<br><li>files of 100 Kb maximum are allowed.</td></tr></table>";
        } else {
            if (move_uploaded_file($_FILES['userImage']['tmp_name'],  $destination_path)) {
                $ret->success = true;
            } else {
                $ret->success = false;
                $ret->err = "An error occurred when uploading the file. It could not be saved.";
            }
        }
        return $ret;
    }

    private function ClientExist($c)
    {
        $clients = $this->_clientRepository->Get();
        foreach ($clients as $client) {
            if (Client::AreEqual($client, $c)) {
                return true;
            }
        }
        return false;
    }
}
