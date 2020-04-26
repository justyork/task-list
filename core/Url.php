<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 25.04.2020
 */

namespace Core;


class Url
{
    private $raw;
    private $data;

    public function __construct($url = null)
    {
        if (!$url)
            $url = $_SERVER['REQUEST_URI'];
        $this->parse($url);
    }

    /**
     * @param $url
     */
    private function parse($url)
    {
        $this->raw = parse_url($url);
        $this->data['path'] = $this->raw['path'];
        $this->params();
    }

    /**
     *
     */
    private function params()
    {
        if (isset($this->raw['query'])) {
            $params = explode('&', $this->raw['query']);
            foreach ($params as $param) {
                [$key, $value] = explode('=', $param);
                $this->data['params'][$key] = $value;
            }
        }
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->data['path'];
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->data['params'] ?? [];
    }

    public function buildLink(array $params): string
    {
        return $this->getPath().($params ? '?'.http_build_query($params) : '');
    }

    public function hasParam($field)
    {
        return isset($this->getParams()[$field]);
    }

    public function get(string $param): ?string
    {
        return $this->getParams()[$param] ?? false;
    }
}
