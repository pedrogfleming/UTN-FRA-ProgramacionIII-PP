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
                    isset($_GET['roomType'])
                ) {
                    $dto = new stdClass;
                    if (
                        isset($_GET["dateFrom"]) &&
                        isset($_GET["dateTo"])
                    ) {
                        $dto->dateFrom = $_GET["dateFrom"];
                        $dto->dateTo = $_GET["dateTo"];
                    } else {
                        $yesterday = new DateTime('yesterday');
                        $yesterdayFormatted = $yesterday->format('Y-m-d');
                        $dto->dateFrom = $yesterday;
                        $dto->dateTo = $yesterday;
                    }
                    $dto->roomType = $_GET["roomType"];

                    if (isset($_GET["clientId"])) {
                        $dto->clientId = $_GET["clientId"];
                    }

                    if (isset($_GET["onlyCanceled"])) {
                        $dto->onlyCanceled = $_GET["onlyCanceled"];
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
            default:
                HttpStatusCodes::SendResponse(['err' => 'Invalid action'],  HttpStatusCodes::BAD_REQUEST);
                break;
        }
    } else {
        HttpStatusCodes::SendResponse(['err' => 'Missing parameter action'],  HttpStatusCodes::BAD_REQUEST);
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

                    if (isset($_POST["paymentMethod"])) {
                        $dto->paymentMethod = $_POST["paymentMethod"];
                    } else {
                        $dto->paymentMethod = "efectivo";
                    }

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
                // 6
            case 'CancelBooking':
                if (
                    isset($_POST["clientId"]) &&
                    isset($_POST["clientType"]) &&
                    isset($_POST["bookingId"])
                ) {
                    $dto = new stdClass;
                    $dto->clientId = $_POST["clientId"];
                    $dto->clientType = $_POST["clientType"];
                    $dto->bookingId = $_POST["bookingId"];

                    $bookingController = new RoomController();
                    $response = $bookingController->Cancel($dto);

                    $statusCode = HttpStatusCodes::OK;
                    if (isset($response->err)) {
                        switch ($response->err) {
                            case 'Unable to modify booking status: Booking id does not exist':
                            case 'Unable to modify booking status: Client id does not exist':
                            case 'Booking not found':
                                $statusCode = HttpStatusCodes::NOT_FOUND;
                                break;
                            case 'Unable to modify the booking':
                                $statusCode = HttpStatusCodes::UNPROCESSABLE_ENTITY;
                                break;
                            default:
                                $statusCode = HttpStatusCodes::INTERNAL_SERVER_ERROR;
                                break;
                        }
                    }
                    HttpStatusCodes::SendResponse($response, $statusCode);
                }
                break;
                // 7
            case 'AdjustBookingAmount':
                if (
                    isset($_POST["bookingId"]) &&
                    isset($_POST["adjustmentReason"]) &&
                    isset($_POST["amountToAdjust"])
                ) {
                    $dto = new stdClass;
                    $dto->bookingId = $_POST["bookingId"];
                    $dto->adjustmentReason = $_POST["adjustmentReason"];
                    $dto->amountToAdjust = $_POST["amountToAdjust"];

                    $bookingController = new RoomController();
                    $response = $bookingController->UpdateAmount($dto);

                    $statusCode = HttpStatusCodes::OK;
                    if (isset($response->err)) {
                        if ($response->err == "Unable to modify booking : Booking id does not exist") {
                            $statusCode = HttpStatusCodes::NOT_FOUND;
                        } else {
                            $statusCode = HttpStatusCodes::UNPROCESSABLE_ENTITY;
                        }
                    }
                    HttpStatusCodes::SendResponse($response, $statusCode);
                }
                break;
            default:
                HttpStatusCodes::SendResponse(['err' => 'Invalid action'],  HttpStatusCodes::BAD_REQUEST);
                break;
        }
    } else {
        HttpStatusCodes::SendResponse(['err' => 'Missing parameter action'],  HttpStatusCodes::BAD_REQUEST);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $_PUT = file_get_contents("php://input");
    $_PUT = json_decode($_PUT, true);

    if (isset($_PUT['action'])) {
        switch ($_PUT['action']) {
                // 5
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
            default:
                HttpStatusCodes::SendResponse(['err' => 'Invalid action'],  HttpStatusCodes::BAD_REQUEST);
                break;
        }
    } else {
        HttpStatusCodes::SendResponse(['err' => 'Missing parameter action'],  HttpStatusCodes::BAD_REQUEST);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $_DELETE = file_get_contents("php://input");
    $_DELETE = json_decode($_DELETE, true);
    if (isset($_DELETE['action'])) {
        switch ($_DELETE['action']) {
            case "DeleteClient":
                if (
                    isset($_DELETE["clientId"]) &&
                    isset($_DELETE["clientType"])
                ) {

                    $dto = new stdClass;
                    $dto->clientId = $_DELETE["clientId"];
                    $dto->clientType = $_DELETE["clientType"];

                    $clientController = new ClientController();
                    $response = $clientController->Delete($dto);

                    $statusCode = HttpStatusCodes::OK;
                    if (isset($response->err)) {
                        $statusCode = $response->err == "Client could not be deleted"
                            ? HttpStatusCodes::UNPROCESSABLE_ENTITY
                            : HttpStatusCodes::NOT_FOUND;
                    }
                    HttpStatusCodes::SendResponse($response, $statusCode);
                }
                break;
            default:
                HttpStatusCodes::SendResponse(['err' => 'Invalid action'],  HttpStatusCodes::BAD_REQUEST);
                break;
        }
    } else {
        HttpStatusCodes::SendResponse(['err' => 'Missing parameter action'],  HttpStatusCodes::BAD_REQUEST);
    }
}
