<?php
class Booking implements \JsonSerializable
{
    private $bookingId;
    private $clientType;
    private $clientId;
    private $checkIn;
    private $checkOut;
    private $roomType;
    private $totalBookingAmount;
    private $status;

    public function __construct(
        $clientType,
        $clientId,
        $checkIn,
        $checkOut,
        $roomType,
        $totalBookingAmount
    ) {
        $this->clientType = $clientType;
        $this->clientId = $clientId;
        $this->checkIn = $checkIn;
        $this->checkOut = $checkOut;
        $this->roomType = $roomType;
        $this->totalBookingAmount = $totalBookingAmount;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public function getBookingId()
    {
        return $this->bookingId;
    }

    public function setBookingId($bookingId)
    {
        $this->bookingId = $bookingId;
    }

    public function getClientType()
    {
        return $this->clientType;
    }

    public function setClientType($clientType)
    {
        $this->clientType = $clientType;
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    public function getCheckIn()
    {
        return $this->checkIn;
    }

    public function setCheckIn($checkIn)
    {
        $this->checkIn = Booking::mapDate($checkIn);
    }

    public function getCheckOut()
    {
        return $this->checkOut;
    }

    public function setCheckOut($checkOut)
    {
        $this->checkOut = Booking::mapDate($checkOut);
    }

    public function getRoomType()
    {
        return $this->roomType;
    }

    public function setRoomType($roomType)
    {
        $this->roomType = $roomType;
    }

    public function getTotalBookingAmount()
    {
        return $this->totalBookingAmount;
    }

    public function setTotalBookingAmount($totalBookingAmount)
    {
        $this->totalBookingAmount = $totalBookingAmount;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public static function AreEqual($a, $b)
    {
        return $a->getBookingId() == $b->getBookingId() ||
            ($a->getClientType() == $b->getClientType() &&
                Booking::mapDate($a->getCheckIn()) == Booking::mapDate($b->getCheckIn()) &&
                Booking::mapDate($a->getCheckOut()) == Booking::mapDate($b->getCheckOut()) &&
                $a->getRoomType() == $b->getRoomType() &&
                $a->getTotalBookingAmount() == $b->getTotalBookingAmount() &&
                $a->getClientId() == $b->getClientId()
            );
    }

    public static function map($arr)
    {
        $ret_arr = [];
        foreach ($arr as $obj) {
            $newBooking = new Booking(
                $obj->clientType,
                $obj->clientId,
                Booking::mapDate($obj->checkIn),
                Booking::mapDate($obj->checkOut),
                $obj->roomType,
                intval($obj->totalBookingAmount)
            );
            $newBooking->setBookingId($obj->bookingId);
            if(isset($obj->status)){
                $newBooking->setStatus($obj->status);
            }
            array_push($ret_arr, $newBooking);
        }
        return $ret_arr;
    }

    // Data can be sdclass from datetime or string with format Y-m-d 
    private static function mapDate($objDate)
    {
        $ret = null;
        if (is_string($objDate)) {
            $ret = $objDate;
        } else if($objDate instanceof DateTime) {
            $ret = $objDate->format('Y-m-d');
        } else {
            // Handle invalid checkIn data
            $ret = null;
        }
        return $ret; 
    }
}
