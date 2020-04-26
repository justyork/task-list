<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 26.04.2020
 */

namespace Core;


interface AuthInterface
{

    public function checkPassword($password);

    public function findByToken($token);

    public function findUser($login);

    public function setToken(string $token);

}
