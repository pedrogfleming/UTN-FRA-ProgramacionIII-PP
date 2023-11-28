<?php
require_once("../Models/User.php");
require_once "../database/dao.php";

class UserRepository
{
    public function Create($u)
    {
        try {
            $objDAO = DAO::GetInstance();
            $command = $objDAO->prepareQuery("INSERT INTO Users (username, mail, password, role) VALUES (?,?,?,?)");
            $passwordHash = password_hash($u->password, PASSWORD_DEFAULT);
            $command->execute([$u->username, $u->mail, $passwordHash, $u->role]);
            return $objDAO->getLastId();
        } catch (PDOException $e) {
            throw new Exception("Unable to register the user: " . $e->getMessage());
        }
    }

    public function Get($id = null)
    {
        try {
            $objDAO = DAO::GetInstance();
            if (isset($id)) {
                $command = $objDAO->prepareQuery("SELECT * FROM Users WHERE id = ? AND isDeleted = 0");
                $command->execute([$id]);
                $result = $command->fetch(PDO::FETCH_OBJ);
                if ($result) {
                    return $result;
                } else {
                    throw new Exception("User not found");
                }
            } else {
                $command = $objDAO->prepareQuery("SELECT * FROM Users WHERE isDeleted = 0");
                $command->execute();
                $results = $command->fetchAll(PDO::FETCH_OBJ);
                return $results;
            }
        } catch (PDOException $e) {
            throw new Exception("Unable to retrieve the user(s): " . $e->getMessage());
        }
    }

    public function Update($user)
    {
        try {
            $objDAO = DAO::GetInstance();
            $command = $objDAO->prepareQuery("UPDATE Users SET username = ?, mail = ?, password = ?, role = ? WHERE id = ? AND isDeleted = 0");
            $command->execute([$user->username, $user->mail, $user->password, $user->role, $user->id]);
            if ($command->rowCount() > 0) {
                return $this->Get($user->id);
            } else {
                throw new Exception("Unable to modify the user or user not found");
            }
        } catch (PDOException $e) {
            throw new Exception("Unable to modify the user: " . $e->getMessage());
        }
    }

    public function Delete($userId, $userRole)
    {
        try {
            $objDAO = DAO::GetInstance();
            $command = $objDAO->prepareQuery("UPDATE Users SET isDeleted = 1 WHERE id = ? AND role = ? AND isDeleted = 0");
            $command->execute([$userId, $userRole]);
            if ($command->rowCount() > 0) {
                return true;
            } else {
                throw new Exception("User id and role combination couldn't be found or user is already deleted");
            }
        } catch (PDOException $e) {
            throw new Exception("Error while performing delete operation: " . $e->getMessage());
        }
    }

    public function UserExist($u)
    {
        $users = User::map($this->Get());
        foreach ($users as $user) {
            if (User::AreEqual($user, $u)) {
                return true;
            }
        }
        return false;
    }
}
