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
require_once("../Controllers/UserController.php");

require_once("../Middlewares/AuthenticationMiddleware.php");
require_once("../Middlewares/AuthorizationMiddleware.php");
require_once("../Middlewares/LoggerAccessMidleware.php");
require_once("../Middlewares/TransactionLoggerMiddleware.php");
require_once  '../vendor/autoload.php';

$app = AppFactory::create();
$app->setBasePath('/app');

// Add error middleware
$app->addErrorMiddleware(true, true, true);


$authmiddleware = new AuthenticationMiddleware();
// Add parse body
$app->addBodyParsingMiddleware();

$app->group('/client', function (RouteCollectorProxy $group) {
    $altaYborradoDeClientes = new AuthorizationMiddleware(["gerente"]);
    $group->post('[/]', \ClientController::class . ':Create')->add($altaYborradoDeClientes);
    $group->get('[/]', \ClientController::class . ':Get')->add(new AuthorizationMiddleware(["recepcionista","cliente"]));
    $group->put('/{client}', \ClientController::class . ':Update');
    $group->delete('/{client}', \ClientController::class. ':Delete')->add($altaYborradoDeClientes);
})->add($authmiddleware)->add(new AccessLoggerMiddleware())->add(new TransactionLoggerMiddleware());

$app->group('/booking', function (RouteCollectorProxy $group) {
    $group->post('[/]', \RoomController::class . ':Book')->add(new AuthorizationMiddleware(["recepcionista"]));
    $group->get('[/]', \RoomController::class . ':Get')->add(new AuthorizationMiddleware(["recepcionista","cliente"]));
    $group->put('/{booking}', \RoomController::class . ':Update');
    $group->delete('/{booking}', \RoomController::class. ':Delete');
})->add($authmiddleware)->add(new AccessLoggerMiddleware())->add(new TransactionLoggerMiddleware());

$app->group('/user', function (RouteCollectorProxy $group) {
    $authmiddleware = new AuthenticationMiddleware();
    $group->post('[/]', \UserController::class . ':Create')->add($authmiddleware)->add(new AccessLoggerMiddleware())->add(new TransactionLoggerMiddleware());
    $group->get('[/]', \UserController::class . ':Get')->add(new AuthorizationMiddleware(["recepcionista","cliente"]))->add($authmiddleware);
    $group->put('/{user}', \UserController::class . ':Update')->add($authmiddleware)->add(new AccessLoggerMiddleware())->add(new TransactionLoggerMiddleware());
    $group->delete('/{user}', \UserController::class. ':Delete')->add($authmiddleware)->add(new AccessLoggerMiddleware())->add(new TransactionLoggerMiddleware());
    $group->post('/login', \UserController::class . ':GenerateToken');
});


$app->run();