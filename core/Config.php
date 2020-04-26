<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 25.04.2020
 */

namespace Core;


class Config extends SingleTone
{
    /**
     * @var []
     */
    protected $config;

    protected $delimeter = '.';

    /**
     * @return mixed
     */
    protected static function getInstance()
    {
        $instance = parent::getInstance();
        if (!$instance->config)
            $instance->config = require(__DIR__.'/../config/main.php');

        return $instance;
    }

    /**
     * @param $name
     * @param null $default
     * @return mixed|null
     */
    public static function get($name, $default = null)
    {
        return static::getInstance()->getConfigRow($name, $default);
    }

    /**
     * @param $name
     * @param null $default
     * @return mixed
     */
    public static function param($name, $default = null)
    {
        return static::getInstance()->getConfigRow($name, $default, 'params');
    }


    /**
     * @param $name
     * @param $default
     * @param null $root
     * @return mixed|null
     */
    protected function getConfigRow($name, $default = null, $root = null)
    {
        $config = $this->config;
        if ($root) {
            $config = $this->unpackConfig($config, $this->pathToArray($root));
            if (!$config)
                return $default;
        }

        $config = $this->unpackConfig($config, $this->pathToArray($name));
        if (!$config)
            return $default;

        return $config;
    }

    /**
     * @param $name
     * @return array|null
     */
    protected function pathToArray($name): ?array
    {
        return explode($this->delimeter, $name);
    }

    /**
     * @param $config
     * @param $arr
     * @return mixed
     */
    protected function unpackConfig($config, $arr)
    {
        foreach ($arr as $item) {
            if (!isset($config[$item]))
                return false;
            $config = $config[$item];
        }
        return $config;
    }

}
