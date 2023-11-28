<?php
require_once("../Repositories/UserRepository.php");

class UserModification
{
    private $_userRepository;

    public function __construct()
    {
        $this->_userRepository = new UserRepository();
    }

    public function ModifyUser($userDTO)
    {
        require_once("../Models/User.php");
        if (isset($userDTO)) {
            $existingUser = $this->_userRepository->Get($userDTO->id);
            if ($existingUser) {
                $existingUser->username = $userDTO->username;
                $existingUser->mail = $userDTO->mail;
                $existingUser->password = $userDTO->password;
                $existingUser->role = $userDTO->role;
                $this->_userRepository->Update($existingUser);
                return $existingUser;
            } else {
                throw new Exception('Unable to modify the user: user does not exist');
            }
        } else {
            throw new Exception('Unable to modify the user: user data must be provided');
        }
    }
}