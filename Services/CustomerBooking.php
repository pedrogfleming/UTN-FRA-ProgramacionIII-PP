<?php
require_once("./Repositories/BookingRepository.php");
require_once("./Repositories/ClientRepository.php");
require_once("./Models/Client.php");
require_once("./Models/Booking.php");

class CustomerBooking
{
    private $_bookingRepository;
    private $_clientRepository;
    public function __construct()
    {
        $this->_bookingRepository = new BookingRepository();
        $this->_clientRepository = new ClientRepository();
    }
    public function Get($clientDTO)
    {
        $ret = new stdClass();
        $clients = $this->_clientRepository->Get();

        $ids = array_filter($clients, function ($obj) use ($clientDTO) {
            return $obj->getId() == $clientDTO->clientId;
        });

        if (count($ids) > 0) {
            $foundedClient = array_filter($ids, function ($obj) use ($clientDTO) {
                return $obj->getClientType() == $clientDTO->clientType;
            });

            if (count($foundedClient) > 0) {
                $foundedClient = array_values($foundedClient);
                $ret = (object) array(
                    'country' => $foundedClient[0]->getCountry(),
                    'city' => $foundedClient[0]->getCity(),
                    'phone' => $foundedClient[0]->getPhoneNumber()
                );
                return $ret;
            } else {
                $ret->err = "Client type incorrect";
            }
        } else {
            $ret->err = "Number and type of client are incorrect";
        }
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
                    $statusImageUpload=$this->UploadImage($fileName);
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
