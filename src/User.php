<?php
namespace Tabula\Modules\Auth;

class User {
    public static function load(int $id): User{
        $user = new User();
        //TODO: load from database
        return $user;
    }

    public static function guest(): User{
        $user = new User();
        //TODO: Fill with guest user data
        return $user;
    }
}