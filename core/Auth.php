<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 26.04.2020
 */

namespace Core;


use Models\User;

class Auth extends SingleTone
{

    protected $user;

    public static function isGuest()
    {
        return static::getInstance()->user === null;
    }

    public static function setUser(User $user)
    {
        static::getInstance()->user = $user;
    }

    public static function clear()
    {
        static::getInstance()->user = null;
    }
}
