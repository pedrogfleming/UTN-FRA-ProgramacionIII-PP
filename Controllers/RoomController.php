<?php
require_once("./Services/CustomerBooking.php");
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

    public function Get($searchCriteria)
    {
        try {
            $customerBooking = new CustomerBooking();
            return $customerBooking->Get($searchCriteria);
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
