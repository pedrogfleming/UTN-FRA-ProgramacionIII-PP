<?php
    class ClientController{
        public function Create($c){
            require_once("./Services/ClientRegistration.php");
            $clientRegistration = new ClientRegistration();
            return $clientRegistration->RegisterClient($c);
        }
    }
?>