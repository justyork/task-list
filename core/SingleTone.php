<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 25.04.2020
 */

namespace Core;


class SingleTone
{
    private static $instances = [];

    protected function __construct() { }
    protected function __clone() { }
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }

    protected static function getInstance()
    {
        $subclass = static::class;
        if (!isset(self::$instances[$subclass])) {
            self::$instances[$subclass] = new static;
        }
        return self::$instances[$subclass];
    }
}
