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
        $this->checkIn = $checkIn;
    }

    public function getCheckOut()
    {
        return $this->checkOut;
    }

    public function setCheckOut($checkOut)
    {
        $this->checkOut = $checkOut;
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

    public static function AreEqual($a, $b)
    {
        return $a->getBookingId() == $b->getBookingId() ||
            ($a->getClientType() == $b->getClientType() &&
                $a->getCheckIn() == $b->getCheckIn() &&
                $a->getCheckOut() == $b->getCheckOut() &&
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
                DateTime::createFromFormat('Y-m-d', $obj->checkIn),
                DateTime::createFromFormat('Y-m-d', $obj->checkOut),
                $obj->roomType,
                intval($obj->totalBookingAmount)
            );
            $newBooking->setBookingId($obj->bookingId);
            array_push($ret_arr, $newBooking);
        }
        return $ret_arr;
    }
}
