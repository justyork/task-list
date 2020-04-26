<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 26.04.2020
 */

namespace Controllers;


use Core\Auth;
use Core\BaseController;
use Models\User;

class AuthController extends BaseController
{
    protected $layout = 'auth';

    public function login()
    {
        $user = new User();
        if ($this->request->post('User')) {
            $data = $this->request->post('User');
            if ($this->auth->login($data['name'], $data['password']))
                $this->redirect('/');
            else
                $user->addError('name', 'Неверные имя пользователя или пароль');
        }

        return $this->render('auth.login', compact('user'));
    }

    public function logout()
    {
        $this->auth->logout();
        $this->redirect('/');
    }
}
