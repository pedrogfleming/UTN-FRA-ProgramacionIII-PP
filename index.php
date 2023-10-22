<?php
require_once("./Controllers/ClientController.php");
require_once("./Helpers/statusCodeHelper.php");
require_once("./Controllers/RoomController.php");

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

                    $statusCode = HttpStatusCodes::CREATED;
                    if(isset($createdClient->err)){
                        $statusCode = HttpStatusCodes::UNPROCESSABLE_ENTITY;
                    }
                    HttpStatusCodes::SendResponse($createdClient, $statusCode);
                }
                break;
                // 2
            case 'GetClient':
                if (
                    isset($_POST['clientId']) &&
                    isset($_POST["clientType"])
                ) {
                    $dto = new stdClass;
                    $dto->clientId = $_POST["clientId"];
                    $dto->clientType = $_POST["clientType"];

                    $clientController = new ClientController();
                    $foundedClient = $clientController->Get($dto);

                    $statusCode = HttpStatusCodes::OK;
                    if(isset($foundedClient->err)){
                        $statusCode = HttpStatusCodes::NOT_FOUND;
                    }
                    HttpStatusCodes::SendResponse($foundedClient, $statusCode);
                }
                break;
                // 3
            case 'BookRoom':
                if (
                    isset($_POST["clientType"]) &&
                    isset($_POST["clientId"]) &&
                    isset($_POST["checkIn"]) && 
                    isset($_POST["checkOut"]) && 
                    isset($_POST["roomType"]) && 
                    isset($_POST["totalBookingAmount"])
                ) {
                    $dto = new stdClass;
                    $dto->clientType = $_POST["clientType"];
                    $dto->clientId = $_POST["clientId"];
                    $dto->checkIn = $_POST["checkIn"];
                    $dto->checkOut = $_POST["checkOut"];
                    $dto->roomType = $_POST["roomType"];
                    $dto->totalBookingAmount = $_POST["totalBookingAmount"];

                    $bookingController = new RoomController();
                    $bookingRoom = $bookingController->Book($dto);

                    $statusCode = HttpStatusCodes::CREATED;
                    if(isset($bookingRoom->err)){
                        $statusCode = HttpStatusCodes::UNPROCESSABLE_ENTITY;
                    }
                    HttpStatusCodes::SendResponse($bookingRoom, $statusCode);
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
