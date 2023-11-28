<?php
require_once("../Repositories/UserRepository.php");

class UserDeletion
{
    private $_userRepository;

    public function __construct()
    {
        $this->_userRepository = new UserRepository();
    }

    public function DeleteUser($userDTO)
    {
        if (isset($userDTO)) {
            $users = $this->_userRepository->Get();
            $filteredUsers = array_filter($users, function ($user) use ($userDTO) {
                return $user->username == $userDTO->username;
            });

            if (!empty($filteredUsers)) {
                $existingUser = reset($filteredUsers);
                $this->_userRepository->Delete($existingUser->id);
                return true;
            } else {
                throw new Exception('Unable to delete the user: user does not exist');
            }
        } else {
            throw new Exception('Unable to delete the user: user data must be provided');
        }
    }
}