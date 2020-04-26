<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 27.04.2020
 */

namespace Core;


trait Response
{
    protected function notFound()
    {
        $this->error(404, 'Not Found');
    }
    protected function unauthorized()
    {
        $this->error(401, 'Unauthorized');
    }

    protected function error($code, $message)
    {
        header("HTTP/1.0 {$code} {$message}");
        die((new View('error', ['code' => $code, 'message' => $message]))->run());
    }
}
