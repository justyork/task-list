<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 26.04.2020
 */

namespace Core;


class Authorization
{
    use Secure;
    /** @var AuthInterface */
    private $model;
    private $loginField;
    private $passwordField;
    private $saltField;
    private $data;

    private $user;

    public function __construct()
    {
        $userConf = Config::get('user.class');
        $this->modelClass = new $userConf();
        $this->loginField = Config::get('user.fields.login', 'login');
        $this->passwordField = Config::get('user.fields.password', 'password]');
        $this->saltField = Config::get('user.fields.salt', 'salt]');

        $this->check();
    }

    public function check()
    {
        if (!$this->user) {
            if ($_COOKIE['token'] ?? $_SESSION['token']) {
                $this->user = $this->modelClass->findByToken($_COOKIE['token'] ?? $_SESSION['token']);
                if($this->user)
                    Auth::setUser($this->user);
            }
        }
    }

    public function login($login, $password)
    {
        $this->user = $this->modelClass->findUser($login);
        if (!$this->user)
            return false;

        if (!$this->user->checkPassword($password))
            return false;

        return $this->authorizate();
    }

    private function findUserByToken($token)
    {
        return $this->modelClass->findByToken($token);
    }

    public function isGuest()
    {
        return isset($this->user);
    }

    public function logout()
    {
        unset($_SESSION['token']);
        setcookie('token', null, time(), '/');
        Auth::clear();
        $this->user = null;
    }

    private function authorizate()
    {
        $token = $this->randomString(32);

        $_SESSION['username'] = $this->user->name;
        $_SESSION['token'] = $token;
        setcookie('token', $token, time() + 3600 * 24, '/');

        $this->user->setToken($token);
        return true;
    }


}
