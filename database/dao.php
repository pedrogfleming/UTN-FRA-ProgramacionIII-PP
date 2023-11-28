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
        $dbname = "db_utn_tp_comanda";
        $dbusername = "root";
        $dbpassword = "";
        $this->PDOobject = new PDO("mysql:host=localhost", $dbusername, $dbpassword);
        $this->PDOobject->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbname = "`" . str_replace("`", "``", $dbname) . "`";
        $this->PDOobject->query("CREATE DATABASE IF NOT EXISTS $dbname");
        $this->PDOobject->query("use $dbname");
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
