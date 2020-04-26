<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 26.04.2020
 */

namespace Core;


trait Secure
{

    /**
     * @param $password
     * @param string $salt
     * @return string
     */
    public function passwordHash($password, $salt = '')
    {
        return sha1($salt.$password);
    }

    /**
     * @return string
     */
    public function createSalt(): string
    {
        return  $this->randomString(32);
    }

    /**
     * @param int $count
     * @param int $type
     * @return string
     */
    public function randomString($count = 10, $type = 1) {
        $chars = '1234567890ZYXWVUTSRQPONMLKJIHGFEDCBAzyxwvutsrqponmlkjihgfedcba';
        if ($type === 2)
            $chars .= '!"№;%:?*()_+=-~/\<>,.[]{}';
        $code = "";
        $clen = strlen($chars) - 1;

        while (strlen($code) < $count)
            $code .= $chars[mt_rand(0, $clen)];

        return $code;
    }


}
