<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 25.04.2020
 */

namespace Models;


use Core\ActiveRecord;
use Core\AuthInterface;
use Core\Secure;

class User extends ActiveRecord implements AuthInterface
{
    use Secure;
    public $fields;

    public function rules()
    {
        return [
            ['auth_token', 'safe']
        ];
    }

    public static function model($className=__CLASS__): ActiveRecord
    {
        return parent::model($className);
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Имя',
            'password' => 'Пароль'
        ];
    }

    /**
     * @param $token
     * @return bool|ActiveRecord
     */
    public function findByToken($token)
    {
        return self::model()->find(['auth_token' => $token]);
    }

    /**
     * @param $login
     * @return bool|ActiveRecord
     */
    public function findUser($login)
    {
        return self::model()->find(['name' => $login]);
    }

    /**
     * @param $password
     * @return bool
     */
    public function checkPassword($password)
    {
        return $this->password === $this->passwordHash($password, $this->salt);
    }

    /**
     * @param string $token
     */
    public function setToken(string $token)
    {
        $this->auth_token = $token;
        $this->save();
    }
}
