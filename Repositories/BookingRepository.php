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
        if (file_exists($this->_fileName)) {
            $bookings = $this->_fileManager->ReadJSON($this->_fileName);
            if (!empty($bookings)) {
                $nextId = BookingRepository::GetNextId($bookings);
                $b->setBookingId($nextId);

                array_push($bookings, $b);
                if ($this->_fileManager->SaveJSON($this->_fileName, $bookings)) {
                    return $this->Get($nextId);
                }
            }
        } else {
            $b->setBookingId($this->_base_id);
            $bookings[0] = $b;
            if ($this->_fileManager->SaveJSON($this->_fileName, $bookings)) {
                $ret = $this->Get($b->getBookingId());
                return $ret;
            } else {
                throw new Exception("Unable to register the booking");
            }
        }
    }

    public function Get($id = null)
    {
        $notFound = array();
        if (file_exists($this->_fileName)) {
            $bookings = Booking::map($this->_fileManager->ReadJSON($this->_fileName));
            if (isset($id)) {
                $targetBooking = $this->SearchById($bookings, $id);
                if ($targetBooking !== false) {
                    return $targetBooking;
                }
                else{
                    throw new Exception("Booking not found");
                }
            } else {
                return $bookings;
            }
        }
        return $notFound;
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
            //get the id of the last booking stored in the json and add 1 to increment the id of the new booking.
            usort($arr, function ($a, $b) {
                return $a->bookingId < $b->bookingId;
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
        $bookings = $this->Get();
        foreach ($bookings as $booking) {
            if (Booking::AreEqual($booking, $b)) {
                return true;
            }
        }
        return false;
    }
}
