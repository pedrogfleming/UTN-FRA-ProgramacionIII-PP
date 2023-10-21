<?php
require_once("./Controllers/ClientController.php");
require_once("./Helpers/statusCodeHelper.php");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
        }
    } else {
        echo json_encode(['error' => 'Falta el parametro action']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
                // 1
            case 'ClientRegistration':
                if (
                    isset($_POST["name"]) &&
                    isset($_POST["lastName"]) &&
                    isset($_POST["documentType"]) &&
                    isset($_POST["documentNumber"]) &&
                    isset($_POST["email"]) &&
                    isset($_POST["clientType"]) &&
                    isset($_POST["country"]) &&
                    isset($_POST["city"]) &&
                    isset($_POST["phoneNumber"])
                ) {
                    $dto = new stdClass;
                    $dto->name = $_POST["name"];
                    $dto->lastName = $_POST["lastName"];
                    $dto->documentType = $_POST["documentType"];
                    $dto->documentNumber = $_POST["documentNumber"];
                    $dto->email = $_POST["email"];
                    $dto->clientType = $_POST["clientType"];
                    $dto->country = $_POST["country"];
                    $dto->city = $_POST["city"];
                    $dto->phoneNumber = $_POST["phoneNumber"];

                    $clientController = new ClientController();
                    $createdClient = $clientController->Create($dto);
                    
                    HttpStatusCodes::SendResponse($createdClient, HttpStatusCodes::OK);
                }
                break;
                // 2
            case 'GetClient':
                if (
                    isset($_POST['id']) &&
                    isset($_POST["clientType"])
                ) {
                    $dto = new stdClass;
                    $dto->id = $_POST["id"];
                    $dto->clientType = $_POST["clientType"];

                    $clientController = new ClientController();
                    $foundedClient = $clientController->Get($dto);
                    
                    HttpStatusCodes::SendResponse($foundedClient, HttpStatusCodes::OK);
                }
                break;
            default:
                echo json_encode(['error' => 'Accion no valida']);
                break;
        }
    } else {
        echo json_encode(['error' => 'Falta el parametro action']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
}
