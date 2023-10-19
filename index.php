<?php
require_once("./Controllers/ClientController.php");

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':


        break;
    case 'POST':
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
            var_dump($clientController->Create($dto));
        }
        break;
    case 'PUT':
        break;
    default:
        echo 'Verbo no permitido';
        break;
}
