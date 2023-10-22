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

    public function Get($dto)
    {
        try {
        } catch (\Throwable $th) {
            $response = new stdClass();
            $response->err = $th->getMessage();
            return $response;
        }
    }
}
