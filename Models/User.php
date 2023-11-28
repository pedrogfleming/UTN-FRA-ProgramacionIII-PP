<?php 
class User {
    public $id;
    public $username;
    public $mail;
    public $password;
    public $role;

    public function __construct($username, $mail, $password, $role)
    {
        $this->username = $username;
        $this->mail = $mail;
        $this->password = $password;
        $this->role = $role;
    }

    public static function map($arr)
    {
        $ret_arr = [];
        foreach ($arr as $obj) {
            $newUser = User::mapObj($obj);
            array_push($ret_arr, $newUser);
        }
        return $ret_arr;
    }
    
    public static function mapObj($obj){
        $newUser = new User(
            $obj->username,
            $obj->mail,
            $obj->password,
            $obj->role
        );
        $newUser->id = $obj->id;
        return $newUser;
    }

    public static function AreEqual($user1, $user2)
    {
        return $user1->username == $user2->username;
    }
    
}