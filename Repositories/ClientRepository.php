<?php
require_once("filesManager.php");
    class ClientRepository{
        private $_fileName;
        private $_base_id = "1000001";

        public function __construct() {
            $this->_fileName = "./hoteles.json";
        }
        public function Create($c){
            require_once("./Models/Client.php");

            if(file_exists($this->_fileName)){
                $clients = ReadJSON($this->_fileName);
                if(!empty($clients)){
                    $nextId = ClientRepository::GetNextId($clients);
                    $c->SetId($nextId);
        
                    array_push($clients, $c);
                    return SaveJSON($this->_fileName, $clients);
                }
            }
            else{                
                $c->SetId($this->_base_id);
                $clients[0] = $c;                
                return SaveJSON($this->_fileName, $clients);
            }
        }

        public function Get($id=null){
            if (isset($id)) {
                # code...
            }
            else{
                if(file_exists($this->_fileName)){
                    $ret = Client::map(ReadJSON($this->_fileName));
                    return $ret;
                }
                else{
                    return array();
                }
            }
        }

        private static function GetNextId($arr){
            if(!empty($arr)){
                //get the id of the last Client stored in the json and add 1 to increment the id of the new Client.
                usort($arr, function($a, $b){
                    return $a->id < $b->id;
                });
                $ret = $arr[0]->id;
                $ret++;
                $ret = strval($ret);
                return $ret;
        }
    }
}
