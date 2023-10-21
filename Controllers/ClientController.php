<?php
    class ClientController{
        public function Create($dto){
            require_once("./Services/ClientRegistration.php");
            $clientRegistration = new ClientRegistration();
            try {
                return $clientRegistration->RegisterClient($dto);
            } catch (\Throwable $th) {
                $response = new stdClass();
                $response->err = $th->getMessage();
                return $response;
            }
        }

        public function Get($dto){
            require_once("./Services/CustomerBooking.php");
            $customerBooking = new CustomerBooking();
            try {
                return $customerBooking->Get($dto);
            } catch (\Throwable $th) {
                $response = new stdClass();
                $response->err = $th->getMessage();
                return $response;
            }
        }
    }
