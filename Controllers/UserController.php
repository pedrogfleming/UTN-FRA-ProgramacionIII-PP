<?php

class UserController
{
    public function Create($request, $response, $args)
    {
        try {
            $params = $request->getParsedBody();
            if (
                isset($params["username"]) &&
                isset($params["mail"]) &&
                isset($params["password"]) &&
                isset($params["role"])
            ) {
                $dto = new stdClass();
                $dto->username = $params["username"];
                $dto->mail = $params["mail"];
                $dto->password = $params["password"];
                $dto->role = $params["role"];

                require_once("../Services/UserRegistration.php");
                $userRegistration = new UserRegistration();

                $result = $userRegistration->RegisterUser($dto);
                $payload = json_encode(array($result));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                throw new Exception("Missing arguments on request");
            }
        } catch (\Throwable $th) {
            $payload = json_encode(array("err" => $th->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public function Get($request, $response, $args)
    {
        require_once("../Services/UserQuery.php");
        try {
            $queryParams = $request->getQueryParams();
            if (
                isset($queryParams['userId']) &&
                isset($queryParams["userRole"])
            ) {
                $dto = new stdClass();
                $dto->userId = $queryParams["userId"];
                $dto->userRole = $queryParams["userRole"];

                $userQuery = new UserQuery();
                $result = $userQuery->Get($dto->userId, $dto->userRole, true);
                $payload = json_encode(array($result));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                throw new Exception("Missing arguments on request");
            }
        } catch (\Throwable $th) {
            $payload = json_encode(array("err" => $th->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public function Update($request, $response, $args)
    {
        try {
            require_once("../Services/UserModification.php");
            $params = $request->getParsedBody();
            if (
                isset($args['user']) &&
                isset($params["username"]) &&
                isset($params["mail"]) &&
                isset($params["password"]) &&
                isset($params["role"])
            ) {
                $dto = new stdClass;
                $dto->username = $params["username"];
                $dto->mail = $params["mail"];
                $dto->password = $params["password"];
                $dto->role = $params["role"];
                $dto->id = $args['user'];

                $userModification = new UserModification();
                $result = $userModification->ModifyUser($dto);
                $payload = json_encode(array($result));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                throw new Exception("Missing arguments on request");
            }
        } catch (\Throwable $th) {
            $payload = json_encode(array("err" => $th->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public function Delete($request, $response, $args)
    {
        try {
            require_once("../Services/UserDeletion.php");
            $params = $request->getParsedBody();
            if (
                isset($params["userId"]) &&
                isset($params["userRole"])
            ) {
                $dto = new stdClass;
                $dto->userId = $params["userId"];
                $dto->userRole = $params["userRole"];

                $userDeletion = new UserDeletion();
                $result = $userDeletion->DeleteUser($dto);
                $payload = json_encode(array($result));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                throw new Exception("Missing arguments on request");
            }
        } catch (\Throwable $th) {
            $payload = json_encode(array("err" => $th->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }
}
