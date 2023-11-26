<?php
require_once("./Repositories/ClientRepository.php");
class ClientRegistration
{
    private $_clientRepository;
    public function __construct()
    {
        $this->_clientRepository = new ClientRepository();
    }
    public function RegisterClient($clientDTO)
    {
        require_once("./Models/Client.php");
        if (isset($clientDTO)) {
            $newClient = new Client($clientDTO->name, $clientDTO->lastName, $clientDTO->documentType, $clientDTO->documentNumber, $clientDTO->email, $clientDTO->clientType, $clientDTO->country, $clientDTO->city, $clientDTO->phoneNumber, $clientDTO->paymentMethod);
            if (!$this->_clientRepository->ClientExist($newClient)) {
                $createdClient =  $this->_clientRepository->Create($newClient);
                if (!empty($createdClient) && isset($createdClient[0])) {
                    $fileName = $createdClient[0]->getId() . $createdClient[0]->getClientType();
                    $statusImageUpload = $this->UploadImage($fileName);
                    if ($statusImageUpload->success) {
                        return $createdClient;
                    } else {
                        throw new Exception('Unable to upload image for client: ' . $statusImageUpload->err);
                    }
                }
                else{
                    throw new Exception('Unable to register the new client: unepexted error');
                }
            } else {
                throw new Exception('Unable to register the new client: client already exist');
            }
        }
    }

private function GuardarFoto($nombreArchivo){
    $ret = new stdClass;
    // La carpeta debe crearse previamente
    $carpeta_archivo = 'ImagenesPedidos/2023/';
    
    // Datos del archivo enviado por POST
    $tipo_archivo =  $_FILES['userImage']['type'];
    $tamano_archivo =  $_FILES['userImage']['size'];

    // Ruta de destino, carpeta + nombre del archivo que quiero guardar
    $ruta_destino = $carpeta_archivo . $nombreArchivo;
    // Realizamos las validaciones del archivo
    if (!((strpos($tipo_archivo, "png") || strpos($tipo_archivo, "jpeg") || strpos($tipo_archivo, "jpg")) && ($tamano_archivo < 100000))) {
        $ret->success = false;
        $ret->err = "La extensión o el tamaño de los archivos no es correcto. <br><br><table><tr><td><li>Solo se permiten archivos .png o .jpg<br><li>Se permiten archivos de un máximo de 100 Kb.</td></tr></table>";
    } else {
        if (move_uploaded_file($_FILES['userImage']['tmp_name'],  $ruta_destino)) {
            $ret->success = true;
        } else {
            $ret->success = false;
            $ret->err = "Se produjo un error al cargar el archivo. No se pudo guardar.";
        }
    }
    return $ret;
}
}
