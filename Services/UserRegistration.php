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
    public function Login($username, $password)
    {
        require_once("../Models/User.php");
        if (isset($username) && isset($password)) {
            $users = User::map($this->_userRepository->Get());
            $existingUser = array_filter($users, function ($user) use ($username) {
                return $user->username == $username;
            });
    
            if (!empty($existingUser)) {
                $existingUser = array_values($existingUser)[0];
                if (password_verify($password, $existingUser->password)) {
                    return true;
                } else {
                    throw new Exception('Invalid password');
                }
            } else {
                throw new Exception('Invalid username');
            }
        } else {
            throw new Exception('Username and password must be provided');
        }
    }
}
