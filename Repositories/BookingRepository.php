<?php
require_once("../Models/Booking.php");
require_once("filesManager.php");
class BookingRepository
{
    private $_fileName;
    private $_fileManager;
    private $_base_id = "1";

    public function __construct()
    {
        $this->_fileName = "../reservas.json";
        $this->_fileManager = new filesManager();
    }
    public function Create($b)
    {
        try {
            $objDAO = DAO::GetInstance();
            $command = $objDAO->prepareQuery("INSERT INTO Bookings (clientType, clientId, checkIn, checkOut, roomType, totalBookingAmount, status) VALUES (?,?,?,?,?,?,?)");
            $command->execute([$b->getClientType(), $b->getClientId(), $b->getCheckIn(), $b->getCheckOut(), $b->getRoomType(), $b->getTotalBookingAmount(), $b->getStatus()]);
            return $objDAO->getLastId();
        } catch (PDOException $e) {
            throw new Exception("Unable to register the booking: " . $e->getMessage());
        }
    }

    public function Get($id = null)
    {
        try {
            $objDAO = DAO::GetInstance();
            if (isset($id)) {
                $command = $objDAO->prepareQuery("SELECT * FROM Bookings WHERE bookingId = ? AND isDeleted = 0");
                $command->execute([$id]);
                $result = $command->fetch(PDO::FETCH_OBJ);
                if ($result) {
                    return $result;
                } else {
                    throw new Exception("Booking not found");
                }
            } else {
                $command = $objDAO->prepareQuery("SELECT * FROM Bookings WHERE isDeleted = 0");
                $command->execute();
                $results = $command->fetchAll(PDO::FETCH_OBJ);
                return $results;
            }
        } catch (PDOException $e) {
            throw new Exception("Unable to retrieve the booking(s): " . $e->getMessage());
        }
    }

    public function Update($bookingId, $booking)
    {
        $bookings = $this->Get();
        $newListBookingModified = BookingRepository::Arr_Update($bookings, $booking);
        if ($newListBookingModified !== false && count($newListBookingModified) > 0) {
            if ($this->_fileManager->SaveJSON($this->_fileName, $newListBookingModified)) {
                return $this->Get($bookingId);
            } else {
                throw new Exception("Unable to modify the booking");
            }
        } else {
            throw new Exception("Booking not found");
        }
    }

    private static function Arr_Update($bookings, $booking)
    {
        for ($i = 0; $i < count($bookings); $i++) {
            if (
                $bookings[$i]->getBookingId() == $booking->getBookingId() &&
                $bookings[$i]->getClientId() == $booking->getClientId() &&
                $bookings[$i]->getClientType() == $booking->getClientType()
            ) {
                $bookings[$i] = $booking;
                return $bookings;
            }
        }
        return false;
    }

    private static function GetNextId($arr)
    {
        if (!empty($arr)) {
            usort($arr, function ($a, $b) {
                return $b->bookingId - $a->bookingId;
            });
            $ret = $arr[0]->bookingId;
            $ret++;
            $ret = strval($ret);
            return $ret;
        }
    }

    private function SearchById($arr, $needle)
    {
        foreach ($arr as $booking) {
            if ($booking->getBookingId() == $needle) {
                return $booking;
            }
        }
        return false;
    }

    public function BookingExist($b)
    {
        $bookings = Booking::map($this->Get());
        foreach ($bookings as $booking) {
            if (Booking::AreEqual($booking, $b)) {
                return true;
            }
        }
        return false;
    }
}
