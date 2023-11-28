<?php
class DAO
{
    private static $dataAccessObject;
    private $PDOobject;

    private function __construct()
    {
        try {
            $this->CreateDb();
        } catch (PDOException $e) {
            print "Error: " . $e->getMessage();
            die();
        }
    }

    private function CreateDb()
    {
        try {
            $dbname = "db_utn_sp_booking_manager";
            $dbusername = "root";
            $dbpassword = "";
            $this->PDOobject = new PDO("mysql:host=localhost", $dbusername, $dbpassword);
            $this->PDOobject->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbname = "`" . str_replace("`", "``", $dbname) . "`";
            $this->PDOobject->query("CREATE DATABASE IF NOT EXISTS $dbname");
            $this->PDOobject->query("use $dbname");
            $create_table_clients = <<<SQL
            CREATE TABLE IF NOT EXISTS Clients (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                lastName VARCHAR(255) NOT NULL,
                documentType VARCHAR(255) NOT NULL,
                documentNumber VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                clientType VARCHAR(255) NOT NULL,
                country VARCHAR(255) NOT NULL,
                city VARCHAR(255) NOT NULL,
                phoneNumber VARCHAR(255) NOT NULL,
                paymentMethod VARCHAR(255) NOT NULL,
                isDeleted BOOLEAN DEFAULT FALSE,
                UNIQUE (email)
            )
            SQL;

            $this->PDOobject->exec($create_table_clients);

            $create_table_bookings = <<<SQL
            CREATE TABLE IF NOT EXISTS Bookings (
                bookingId INT AUTO_INCREMENT PRIMARY KEY,
                clientType VARCHAR(255) NOT NULL,
                clientId INT NOT NULL,
                checkIn DATETIME NOT NULL,
                checkOut DATETIME,
                roomType VARCHAR(255) NOT NULL,
                totalBookingAmount DECIMAL(10, 2) NOT NULL,
                status VARCHAR(255) NOT NULL,
                isDeleted BOOLEAN DEFAULT FALSE,
                FOREIGN KEY (clientId) REFERENCES Clients(id)
            )
            SQL;

            $this->PDOobject->exec($create_table_bookings);

            $create_table_booking_changes = <<<SQL
            CREATE TABLE IF NOT EXISTS BookingChanges (
                id INT AUTO_INCREMENT PRIMARY KEY,
                bookingId INT NOT NULL,
                adjustmentReason VARCHAR(255) NOT NULL,
                amountToAdjust DECIMAL(10, 2) NOT NULL,
                isDeleted BOOLEAN DEFAULT FALSE,
                FOREIGN KEY (bookingId) REFERENCES Bookings(bookingId)
            )
            SQL;

            $this->PDOobject->exec($create_table_booking_changes);
        } catch (PDOException $e) {
            throw new Exception("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (!isset(self::$dataAccessObject)) {
            self::$dataAccessObject = new DAO();
        }
        return self::$dataAccessObject;
    }

    public function prepareQuery($sql)
    {
        return $this->PDOobject->prepare($sql);
    }

    public function getLastId()
    {
        return $this->PDOobject->lastInsertId();
    }

    public function __clone()
    {
        trigger_error('ERROR: Cloning of this object is not allowed', E_USER_ERROR);
    }
}
