<?php
require_once("../Repositories/ClientRepository.php");
class ClientRegistration
{
    private $_clientRepository;
    public function __construct()
    {
        $this->_clientRepository = new ClientRepository();
    }
    public function RegisterClient($clientDTO)
    {
        require_once("../Models/Client.php");
        if (isset($clientDTO)) {
            $newClient = new Client(
                $clientDTO->name,
                $clientDTO->lastName,
                $clientDTO->documentType,
                $clientDTO->documentNumber,
                $clientDTO->email,
                $clientDTO->clientType,
                $clientDTO->country,
                $clientDTO->city,
                $clientDTO->phoneNumber,
                null,   //id
                $clientDTO->paymentMethod
            );
            if (!$this->_clientRepository->ClientExist($newClient)) {
                $idCreatedClient = $this->_clientRepository->Create($newClient);
                $createdClientData = $this->_clientRepository->Get($idCreatedClient);
                if (!empty($createdClientData)) {
                    $clients = [];
                    array_push($clients, $createdClientData);
                    $createdClient = Client::map($clients);
                    $fileName = $createdClient[0]->getId() . $createdClient[0]->getClientType();
                    $statusImageUpload = $this->UploadImage($fileName);
                    if ($statusImageUpload->success) {
                        return $createdClient;
                    } else {
                        throw new Exception('Unable to upload image for client: ' . $statusImageUpload->err);
                    }
                } else {
                    throw new Exception('Unable to register the new client: unepexted error');
                }
            } else {
                throw new Exception('Unable to register the new client: client already exist');
            }
        }
    }

    public function UploadImage($file_name)
    {
        $ret = new stdClass;
        // The folder must be created before
        $file_folder = '../ImagenesDeClientes/2023/';

        // Data from the file sent by POST
        $file_type =  $_FILES['userImage']['type'];
        $file_size =  $_FILES['userImage']['size'];

        // Destination path, folder + name of the file I want to save
        $destination_path = $file_folder . $file_name;
        // We perform the validations of the file
        if (!((strpos($file_type, "png") || strpos($file_type, "jpeg") || strpos($file_type, "jpg")) && ($file_size < 300000))) {
            $ret->success = false;
            $ret->err = "The extension or the size of the files is not correct. <br><br><table><tr><td><li>.png or .jpg files are allowed<br><li>files of 300 Kb maximum are allowed.</td></tr></table>";
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
}
