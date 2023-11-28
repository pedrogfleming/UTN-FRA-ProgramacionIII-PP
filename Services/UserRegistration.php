<?php
require_once("../Repositories/UserRepository.php");

class UserRegistration
{
    private $_userRepository;

    public function __construct()
    {
        $this->_userRepository = new UserRepository();
    }

    public function RegisterUser($userDTO)
    {
        require_once("../Models/User.php");
        if (isset($userDTO)) {
            $newUser = new User(
                $userDTO->username,
                $userDTO->mail,
                $userDTO->password,
                $userDTO->role
            );
            if (!$this->_userRepository->UserExist($newUser)) {
                $idCreatedUser = $this->_userRepository->Create($newUser);
                $createdUserData = User::mapObj($this->_userRepository->Get($idCreatedUser));
                if (!empty($createdUserData)) {
                    $users = [];
                    $partialData = new stdClass();
                    $partialData->username = $createdUserData->username;
                    $partialData->id = $createdUserData->id;
                    array_push($users, $partialData);
                    return $users;
                } else {
                    throw new Exception('Unable to register the new user: unexpected error');
                }
            } else {
                throw new Exception('Unable to register the new user: user already exists');
            }
        }
    }
}
