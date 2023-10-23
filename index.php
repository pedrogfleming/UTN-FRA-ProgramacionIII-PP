<?php
require_once("./Controllers/ClientController.php");
require_once("./Helpers/statusCodeHelper.php");
require_once("./Controllers/RoomController.php");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
                // 4
            case 'GetBookings':
                if (
                    isset($_GET['roomType']) &&
                    isset($_GET["dateFrom"]) &&
                    isset($_GET["dateTo"])
                ) {
                    $dto = new stdClass;
                    $dto->roomType = $_GET["roomType"];
                    $dto->dateFrom = $_GET["dateFrom"];
                    $dto->dateTo = $_GET["dateTo"];
                    if (isset($_GET["clientId"])) {
                        $dto->clientId = $_GET["clientId"];
                    }


                    $roomController = new RoomController();
                    $response = $roomController->Get($dto);

                    $statusCode = HttpStatusCodes::OK;
                    if (isset($response->err) || count($response->bookings) < 1) {
                        $statusCode = HttpStatusCodes::NOT_FOUND;
                    }
                    HttpStatusCodes::SendResponse($response, $statusCode);
                }
                break;
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
                    $response = $clientController->Create($dto);

                    $statusCode = HttpStatusCodes::CREATED;
                    if (isset($response->err)) {
                        $statusCode = HttpStatusCodes::UNPROCESSABLE_ENTITY;
                    }
                    HttpStatusCodes::SendResponse($response, $statusCode);
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
                    $response = $clientController->Get($dto);

                    $statusCode = HttpStatusCodes::OK;
                    if (isset($response->err)) {
                        $statusCode = HttpStatusCodes::NOT_FOUND;
                    }
                    HttpStatusCodes::SendResponse($response, $statusCode);
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
                    $response = $bookingController->Book($dto);

                    $statusCode = HttpStatusCodes::CREATED;
                    if (isset($response->err)) {
                        $statusCode = HttpStatusCodes::UNPROCESSABLE_ENTITY;
                    }
                    HttpStatusCodes::SendResponse($response, $statusCode);
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
    $_PUT = file_get_contents("php://input");
    $_PUT = json_decode($_PUT, true);

    if (isset($_PUT['action'])) {
        switch ($_PUT['action']) {
            case 'UpdateClient':
                if (
                    isset($_PUT["clientId"]) &&
                    isset($_PUT["name"]) &&
                    isset($_PUT["lastName"]) &&
                    isset($_PUT["documentType"]) &&
                    isset($_PUT["documentNumber"]) &&
                    isset($_PUT["email"]) &&
                    isset($_PUT["clientType"]) &&
                    isset($_PUT["country"]) &&
                    isset($_PUT["city"]) &&
                    isset($_PUT["phoneNumber"])
                ) {
                    $dto = new stdClass;
                    $dto->clientId = $_PUT["clientId"];
                    $dto->name = $_PUT["name"];
                    $dto->lastName = $_PUT["lastName"];
                    $dto->documentType = $_PUT["documentType"];
                    $dto->documentNumber = $_PUT["documentNumber"];
                    $dto->email = $_PUT["email"];
                    $dto->clientType = $_PUT["clientType"];
                    $dto->country = $_PUT["country"];
                    $dto->city = $_PUT["city"];
                    $dto->phoneNumber = $_PUT["phoneNumber"];

                    $clientController = new ClientController();
                    $response = $clientController->Update($dto);

                    $statusCode = HttpStatusCodes::OK;
                    if (isset($response->err)) {
                        $statusCode = $response->err == "Unable to modify the client"
                            ? HttpStatusCodes::UNPROCESSABLE_ENTITY
                            : HttpStatusCodes::NOT_FOUND;
                    }
                    HttpStatusCodes::SendResponse($response, $statusCode);
                }
                break;
        }
    }
}
