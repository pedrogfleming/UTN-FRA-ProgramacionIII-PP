<?php
require_once("../Models/BookingChange.php");
require_once("filesManager.php");

class BookingAdjustmentHistoryRepository
{
    private $_fileName;
    private $_fileManager;
    private $_base_id = "101";

    public function __construct()
    {
        $this->_fileName = "../ajustes.json";
        $this->_fileManager = new filesManager();
    }

    public function Create($bc){
        if (file_exists($this->_fileName)){
            $bookingsChanges = BookingChange::map($this->_fileManager->ReadJSON($this->_fileName));

            if (!empty($bookingsChanges)) {
                $nextId = BookingAdjustmentHistoryRepository::GetNextId($bookingsChanges);
                $bc->setId($nextId);

                array_push($bookingsChanges, $bc);
                if ($this->_fileManager->SaveJSON($this->_fileName, $bookingsChanges)) {
                    return $bc;
                }
            }
        }
        else{
            $bc->setId($this->_base_id);
            $bookingsChanges[0] = $bc;
            if ($this->_fileManager->SaveJSON($this->_fileName, $bookingsChanges)) {
                return $bc;
            }
            else{
                throw new Exception("Unable to register the booking change");
            }
        }
    }

    private static function GetNextId($arr)
    {
        if (!empty($arr)) {
            usort($arr, function ($a, $b) {
                return $b->getId() - $a->getId();
            });
            $ret = $arr[0]->getId();
            $ret++;
            $ret = strval($ret);
            return $ret;
        }
    }
}
