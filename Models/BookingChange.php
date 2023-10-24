<?php
class BookingChange implements \JsonSerializable
{
    private $id;
    private $clientId;
    private $clientType;
    private $bookingId;
    private $adjustmentReason;
    private $amountToAdjust;

    public function __construct($clientId, $clientType, $bookingId, $adjustmentReason, $amountToAdjust)
    {
        $this->clientId = $clientId;
        $this->clientType = $clientType;
        $this->bookingId = $bookingId;
        $this->adjustmentReason = $adjustmentReason;
        $this->amountToAdjust = $amountToAdjust;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    public function getClientType()
    {
        return $this->clientType;
    }

    public function setClientType($clientType)
    {
        $this->clientType = $clientType;
    }

    public function getBookingId()
    {
        return $this->bookingId;
    }

    public function setBookingId($bookingId)
    {
        $this->bookingId = $bookingId;
    }

    public function getAdjustmentReason()
    {
        return $this->adjustmentReason;
    }

    public function setAdjustmentReason($adjustmentReason)
    {
        $this->adjustmentReason = $adjustmentReason;
    }

    public function getAmountToAdjust()
    {
        return $this->amountToAdjust;
    }

    public function setAmountToAdjust($amountToAdjust)
    {
        $this->amountToAdjust = $amountToAdjust;
    }

    public static function map($arr)
    {
        $ret_arr = [];
        foreach ($arr as $obj) {
            $newBookingChange = new BookingChange(
                $obj->clientId,
                $obj->clientType,
                $obj->bookingId,
                $obj->adjustmentReason,
                $obj->amountToAdjust
            );
            $newBookingChange->setId($obj->id);
            array_push($ret_arr, $newBookingChange);
        }
        return $ret_arr;
    }
}
