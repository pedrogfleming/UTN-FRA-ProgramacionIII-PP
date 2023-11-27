<?php
class ClientDelete
{
    private $_clientRepository;

    public function __construct()
    {
        $this->_clientRepository = new ClientRepository();
    }
    public function Delete($clientDTO)
    {
        require_once("./Models/Client.php");
        $targetClient = $this->_clientRepository->Get($clientDTO->clientId);
        if (!empty($targetClient) && isset($targetClient[0])) {
            $clientDeleted = $this->_clientRepository->Delete($targetClient[0]->getId(), $targetClient[0]->getClientType());
            if ($clientDeleted) {
                $fileName = $targetClient[0]->getId() . $targetClient[0]->getClientType();

                $sourcePath = 'ImagenesDeClientes/2023/' . $fileName;
                $destinationPath = 'ImagenesBackupClientes/2023/' . $fileName; // Ruta completa de la nueva ubicación

                if (!rename($sourcePath, $destinationPath)) {
                    throw new Exception("Couldnt move the photo of the deleted client to the backup location");
                }
                return $clientDeleted;
            } else {
                throw new Exception("Client could not be deleted");
            }
        } else {
            throw new Exception("Client could not be deleted: client id not found");
        }
    }
}
