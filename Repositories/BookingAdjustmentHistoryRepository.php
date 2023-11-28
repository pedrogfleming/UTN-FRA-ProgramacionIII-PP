<?php
require_once("../Models/BookingChange.php");

class BookingAdjustmentHistoryRepository
{
    public function Create($bc)
    {
        try {
            $objDAO = DAO::GetInstance();
            $command = $objDAO->prepareQuery("INSERT INTO BookingChanges (bookingId, adjustmentReason, amountToAdjust) VALUES (?, ?, ?)");
            $command->execute([$bc->getBookingId(), $bc->getAdjustmentReason(), $bc->getAmountToAdjust()]);
            if ($command->rowCount() > 0) {
                $bc->setId($objDAO->getLastId());
                $bookingChange = BookingChange::mapObj($this->Get($objDAO->getLastId()));
                return $bookingChange;
            } else {
                throw new Exception("Unable to register the booking change");
            }
        } catch (PDOException $e) {
            throw new Exception("Unable to register the booking change: " . $e->getMessage());
        }
    }

    public function Get($id)
    {
        try {
            $objDAO = DAO::GetInstance();
            $command = $objDAO->prepareQuery("SELECT * FROM BookingChanges WHERE id = ? AND isDeleted = 0");
            $command->execute([$id]);
            $result = $command->fetch(PDO::FETCH_OBJ);
            if ($result) {
                $bc = new BookingChange($result->bookingId, $result->adjustmentReason, $result->amountToAdjust);
                $bc->setId($result->id);
                return $bc;
            } else {
                throw new Exception("Booking change not found");
            }
        } catch (PDOException $e) {
            throw new Exception("Unable to get the booking change: " . $e->getMessage());
        }
    }
}
