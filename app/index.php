<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require_once("../Controllers/ClientController.php");
require_once("../Controllers/RoomController.php");
// require_once("./Helpers/statusCodeHelper.php");

// Load ENV
// $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
// $dotenv->safeLoad();

require_once  '../vendor/autoload.php';

$app = AppFactory::create();
$app->setBasePath('/app');

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

$app->group('/client', function (RouteCollectorProxy $group) {
    $group->post('[/]', \ClientController::class . ':Create');
    $group->get('[/]', \ClientController::class . ':Get');
    $group->put('/{client}', \ClientController::class . ':Update');
    $group->delete('/{client}', \ClientController::class. ':Delete');
});

$app->group('/booking', function (RouteCollectorProxy $group) {
    $group->post('[/]', \RoomController::class . ':Book');
    $group->get('[/]', \RoomController::class . ':Get');
    $group->put('/{booking}', \RoomController::class . ':Update');
    $group->delete('/{booking}', \RoomController::class. ':Delete');
});

// $app->get('/hello', function ($request, $response, array $args) {
//     try {
//         require_once "../database/dao.php";
//         $dao = DAO::getInstance();
//         $response->getBody()->write('Funciona!');
//         return $response;
//     } catch (\Throwable $th) {
//         $payload = json_encode(array("err" => $th->getMessage()));
//         $response->getBody()->write($payload);
//         return $response->withHeader('Content-Type', 'application/json');
//     }
// });
$app->run();