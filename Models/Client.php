<?php
class Client implements \JsonSerializable
{
    private $id;
    private $name;
    private $lastName;
    private $documentType;
    private $documentNumber;
    private $email;
    private $clientType;
    private $country;
    private $city;
    private $phoneNumber;
    private $paymentMethod;


    public function __construct($name, $lastName, $documentType, $documentNumber, $email, $clientType, $country, $city, $phoneNumber, $id = null, $paymentMethod = "efectivo")
    {
        $this->name = $name;
        $this->lastName = $lastName;
        $this->documentType = $documentType;
        $this->documentNumber = $documentNumber;
        $this->email = $email;
        if(str_contains($clientType, '-')){
            $this->clientType = $clientType;
        }
        else{
            $this->setClientType($clientType);
        }

        $this->country = $country;
        $this->city = $city;
        $this->phoneNumber = $phoneNumber;
        $this->id = $id;    
        $this->paymentMethod = $paymentMethod;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod($p)
    {
        $this->paymentMethod = $p;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    public function getDocumentType()
    {
        return $this->documentType;
    }

    public function setDocumentType($documentType)
    {
        $this->documentType = $documentType;
    }

    public function getDocumentNumber()
    {
        return $this->documentNumber;
    }

    public function setDocumentNumber($documentNumber)
    {
        $this->documentNumber = $documentNumber;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getClientType()
    {
        return $this->clientType;
    }

    public function setClientType($clientType)
    {
        $this->clientType = $clientType . "-" .  $this->documentType;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry($country)
    {
        $this->country = $country;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setCity($city)
    {
        $this->city = $city;
    }

    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    public static function AreEqual($a, $b)
    {
        return ($a->getDocumentNumber() == $b->getDocumentNumber()) ||
            $a->getId() == $b->getId();
    }

    public static function map($arr)
    {
        $ret_arr = [];

        foreach ($arr as $obj) {
            $isDeleted = property_exists($obj,'isDeleted');
            $isDeleted = $isDeleted ? $obj->isDeleted : false;
            if(!$isDeleted){
                $newClient = new Client(
                    $obj->name,
                    $obj->lastName,
                    $obj->documentType,
                    $obj->documentNumber,
                    $obj->email,
                    $obj->clientType,
                    $obj->country,
                    $obj->city,
                    $obj->phoneNumber,
                    $obj->id,
                    $obj->paymentMethod
                );
                array_push($ret_arr, $newClient);
            }
        }
        return $ret_arr;
    }
}
