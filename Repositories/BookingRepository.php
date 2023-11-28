<?php
require_once("../Models/Booking.php");
class BookingRepository
{
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

    public function Update($booking)
    {
        try {
            $objDAO = DAO::GetInstance();
            $command = $objDAO->prepareQuery("UPDATE Bookings SET clientType = ?, clientId = ?, checkIn = ?, checkOut = ?, roomType = ?, totalBookingAmount = ?, status = ? WHERE bookingId = ? AND isDeleted = 0");
            $command->execute([$booking->getClientType(), $booking->getClientId(), $booking->getCheckIn(), $booking->getCheckOut(), $booking->getRoomType(), $booking->getTotalBookingAmount(), $booking->getStatus(), $booking->getBookingId()]);
            if ($command->rowCount() > 0) {
                return $this->Get($booking->getBookingId());
            } else {
                throw new Exception("Unable to modify the booking or booking not found");
            }
        } catch (PDOException $e) {
            throw new Exception("Unable to modify the booking: " . $e->getMessage());
        }
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
