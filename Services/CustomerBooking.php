<?php
require_once("./Repositories/BookingRepository.php");
require_once("./Repositories/ClientRepository.php");
require_once("./Repositories/BookingAdjustmentHistoryRepository.php");
require_once("./Models/Client.php");
require_once("./Models/Booking.php");
require_once("./Models/BookingChange.php");

class CustomerBooking
{
    private $_bookingRepository;
    private $_clientRepository;
    private $_historyAdjustmentRepository;
    public function __construct()
    {
        $this->_bookingRepository = new BookingRepository();
        $this->_clientRepository = new ClientRepository();
        $this->_historyAdjustmentRepository = new BookingAdjustmentHistoryRepository();
    }
    public function Get($searchCriteria)
    {
        $ret = new stdClass();
        $bookings = $this->_bookingRepository->Get();

        // Filter by cancelled bookings
        if(isset($searchCriteria->onlyCanceled)){
            $bookings = array_filter($bookings, function ($obj){
                return $obj->getStatus() == "Cancelled";
            });
            $bookings = array_values($bookings);
        }

        // Filter by client
        if (isset($searchCriteria->clientId)) {
            $bookings = array_filter($bookings, function ($obj) use ($searchCriteria) {
                return $obj->getClientId() == $searchCriteria->clientId;
            });
            $bookings = array_values($bookings);
        }

        // Filter by room type
        if ($searchCriteria->roomType != "Todos") {
            $bookings = array_filter($bookings, function ($obj) use ($searchCriteria) {
                return $obj->getRoomType() == $searchCriteria->roomType;
            });
            $bookings = array_values($bookings);
        }

        // Filter by range of dates (%%% means wildcard, doestn matter the date)
        if ($searchCriteria->dateFrom !== "%%%" || $searchCriteria->dateTo !== "%%%") {
            $bookings = array_filter($bookings, function ($obj) use ($searchCriteria) {
                return DateTime::createFromFormat('Y-m-d', $obj->getCheckIn()) >= DateTime::createFromFormat('Y-m-d', $searchCriteria->dateFrom) &&
                    DateTime::createFromFormat('Y-m-d', $obj->getCheckOut()) <= DateTime::createFromFormat('Y-m-d', $searchCriteria->dateTo);
            });
            $bookings = array_values($bookings);
        }

        $totalBookingAmount = array_reduce($bookings, function ($carry, $b) {
            return $carry + $b->getTotalBookingAmount();
        }, 0);


        // order by checkin date
        usort($bookings, function ($a, $b) {
            return $a->getCheckIn() > $b->getCheckIn();
        });

        $ret->bookings = $bookings;
        $ret->totalBookingAmount = $totalBookingAmount;

        return $ret;
    }

    public function ReserveRoom($dto)
    {
        $ret = new stdClass();
        if (isset($dto)) {
            $booking = new Booking(
                $dto->clientType,
                $dto->clientId,
                $dto->checkIn,
                $dto->checkOut,
                $dto->roomType,
                $dto->totalBookingAmount
            );
            if (!$this->ClientExistsById($booking->getClientId(), $booking->getClientType())) {
                throw new Exception('Unable to book the room: client does not exist');
            } else if (!$this->_bookingRepository->BookingExist($booking)) {
                $createdBooking =  $this->_bookingRepository->Create($booking);
                if (!empty($createdBooking)) {
                    $fileName = $createdBooking->getBookingId() . $createdBooking->getClientType() . $createdBooking->getClientId();
                    $statusImageUpload = $this->UploadImage($fileName);
                    if ($statusImageUpload->success) {
                        return $createdBooking;
                    } else {
                        throw new Exception('Unable to upload image for booking: ' . $statusImageUpload->err);
                    }
                }
            } else {
                throw new Exception('Unable to book the room: booking already exist');
            }
        }
    }

    public function CancelBooking($dto)
    {
        if (isset($dto)) {
            if ($this->ClientExistsById($dto->clientId, $dto->clientType)) {
                $targetBooking = $this->_bookingRepository->Get($dto->bookingId);
                if ($targetBooking !== false) {
                    $targetBooking->setStatus('Cancelled');
                    return $this->_bookingRepository->Update($dto->bookingId, $targetBooking);
                } else {
                    throw new Exception('Unable to modify booking status: Booking id does not exist');
                }
            } else {
                throw new Exception('Unable to modify booking status: Client id does not exist');
            }
        }
    }

    public function UpdateAmount($dto)
    {
        $booking = $this->_bookingRepository->Get($dto->bookingId);
        if (!empty($booking) && is_object($booking)) {
            $booking->setStatus('Modificada');
            $booking->setTotalBookingAmount(intval($dto->amountToAdjust));
            $modifiedBooking = $this->_bookingRepository->Update($dto->bookingId, $booking);
            if (is_object($modifiedBooking) && $modifiedBooking->getBookingId() === $dto->bookingId) {
                $bookingChange = new BookingChange(
                    $dto->bookingId,
                    $dto->adjustmentReason,
                    intval($dto->amountToAdjust)
                );
                return $this->_historyAdjustmentRepository->Create($bookingChange);
            }
        } else {
            throw new Exception('Unable to modify booking : Booking id does not exist');
        }
    }

    public function ClientExistsById($clientId, $clientType)
    {
        $clients = $this->_clientRepository->Get();
        foreach ($clients as $client) {
            if (
                $client->getId() == $clientId &&
                $client->getClientType() == $clientType
            ) {
                return true;
            }
        }
        return false;
    }
    function UploadImage($file_name)
    {
        $ret = new stdClass;
        // The folder must be created before
        $file_folder = 'ImagenesDeReservas/2023/';

        // Data from the file sent by POST
        $file_type =  $_FILES['bookingImage']['type'];
        $file_size =  $_FILES['bookingImage']['size'];

        // Destination path, folder + name of the file I want to save
        $destination_path = $file_folder . $file_name;
        // We perform the validations of the file
        if (!((strpos($file_type, "png") || strpos($file_type, "jpeg")) && ($file_size < 100000))) {
            $ret->success = false;
            $ret->err = "The extension or the size of the files is not correct. <br><br><table><tr><td><li>.png or .jpg files are allowed<br><li>files of 100 Kb maximum are allowed.</td></tr></table>";
        } else {
            if (move_uploaded_file($_FILES['bookingImage']['tmp_name'],  $destination_path)) {
                $ret->success = true;
            } else {
                $ret->success = false;
                $ret->err = "An error occurred when uploading the file. It could not be saved.";
            }
        }
        return $ret;
    }
}
