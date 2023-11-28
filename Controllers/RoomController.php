<?php
require_once("../Services/CustomerBooking.php");
class RoomController
{
    public function Book($dto)
    {
        try {
            $customerBooking = new CustomerBooking();
            return $customerBooking->ReserveRoom($dto);
        } catch (\Throwable $th) {
            $response = new stdClass();
            $response->err = $th->getMessage();
            return $response;
        }
    }

    public function Get($request, $response, $args)
    {


        try {
            $queryParams = $request->getQueryParams();
            if (
                isset($queryParams['roomType'])
            ) {
                $dtoSearchCriteria = new stdClass;
                if (
                    isset($queryParams["dateFrom"]) &&
                    isset($queryParams["dateTo"])
                ) {
                    $dtoSearchCriteria->dateFrom = $queryParams["dateFrom"];
                    $dtoSearchCriteria->dateTo = $queryParams["dateTo"];
                } else {
                    $yesterday = new DateTime('yesterday');
                    $yesterdayFormatted = $yesterday->format('Y-m-d');
                    $dtoSearchCriteria->dateFrom = $yesterday;
                    $dtoSearchCriteria->dateTo = $yesterday;
                }
                $dtoSearchCriteria->roomType = $queryParams["roomType"];

                if (isset($args["clientId"])) {
                    $dtoSearchCriteria->clientId = $args["clientId"];
                }

                if (isset($queryParams["onlyCanceled"])) {
                    $dtoSearchCriteria->onlyCanceled = $queryParams["onlyCanceled"];
                }
                $customerBooking = new CustomerBooking();
                $result = $customerBooking->Get($dtoSearchCriteria);
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

    public function UpdateAmount($dto)
    {
        try {
            $customerBooking = new CustomerBooking();
            return $customerBooking->UpdateAmount($dto);
        } catch (\Throwable $th) {
            $response = new stdClass();
            $response->err = $th->getMessage();
            return $response;
        }
    }

    public function Cancel($dto)
    {
        try {
            $customerBooking = new CustomerBooking();
            return $customerBooking->CancelBooking($dto);
        } catch (\Throwable $th) {
            $response = new stdClass();
            $response->err = $th->getMessage();
            return $response;
        }
    }
}
