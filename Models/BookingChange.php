<?php
class BookingChange implements \JsonSerializable
{
    private $id;
    private $bookingId;
    private $adjustmentReason;
    private $amountToAdjust;

    public function __construct($bookingId, $adjustmentReason, $amountToAdjust)
    {
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
