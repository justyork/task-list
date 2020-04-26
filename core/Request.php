<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 26.04.2020
 */

namespace Core;


class Request
{

    public function get($param, $default = null)
    {
        return $_GET[$param] ?? $default;
    }

    public function post($attr = false, $default = null)
    {
        if (!$attr) {
            return !empty($_POST);
        }

        return $_POST[$attr] ?? $default;
    }


}
