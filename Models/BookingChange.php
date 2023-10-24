<?php
class BookingChange implements \JsonSerializable
{
    private $_id;
    private $_clientId;
    private $_clientType;
    private $_bookingId;
    private $_adjustmentReason;
    private $_amountToAdjust;

    public function __construct($clientId, $clientType, $bookingId, $adjustmentReason, $amountToAdjust)
    {
        $this->_clientId = $clientId;
        $this->_clientType = $clientType;
        $this->_bookingId = $bookingId;
        $this->_adjustmentReason = $adjustmentReason;
        $this->_amountToAdjust = $amountToAdjust;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
    }

    public function getClientId()
    {
        return $this->_clientId;
    }

    public function setClientId($clientId)
    {
        $this->_clientId = $clientId;
    }

    public function getClientType()
    {
        return $this->_clientType;
    }

    public function setClientType($clientType)
    {
        $this->_clientType = $clientType;
    }

    public function getBookingId()
    {
        return $this->_bookingId;
    }

    public function setBookingId($bookingId)
    {
        $this->_bookingId = $bookingId;
    }

    public function getAdjustmentReason()
    {
        return $this->_adjustmentReason;
    }

    public function setAdjustmentReason($adjustmentReason)
    {
        $this->_adjustmentReason = $adjustmentReason;
    }

    public function getAmountToAdjust()
    {
        return $this->_amountToAdjust;
    }

    public function setAmountToAdjust($amountToAdjust)
    {
        $this->_amountToAdjust = $amountToAdjust;
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
