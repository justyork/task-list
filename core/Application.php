<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 25.04.2020
 */

namespace Core;



use ActiveRecord;

class Application
{

    use Response;

    public function run()
    {
        DB::set();
        echo $this->loadController();

    }


    /**
     * @return mixed
     */
    public static function baseDir()
    {
        return $_SERVER['DOCUMENT_ROOT'];
    }

    /**
     * @return string
     */
    public function loadController(): ?string
    {
        $request = new Url($_SERVER['REQUEST_URI']);

        $_GET = $request->getParams();
        [$controller, $action] = $this->findController($request->getPath());

        if (!$controller)
            return  '';

        $obj = new $controller();

        $access = $obj->access();

        if (!empty($access) && $access[1] === '@' && in_array($action, $access[0], true)){
            if (Auth::isGuest()){
                $this->unauthorized();
            }
        }

        $obj->init();

        return $obj->{$action}();
    }

    /**
     * @param $path
     * @return array
     */
    private function findController($path)
    {
        $routes = Config::get('routes', []);
        if (!isset($routes['/']))
            $routes['/'] = 'IndexController@index';

        $pathVariants = $this->slashVariants($path);
        foreach ($routes as $key => $route) {
            if (in_array($key, $pathVariants, true)) {
                [$controller, $action] = $this->parseRoute($route);
                return [$this->findControllerPath($controller), $action];
            }
        }

        $clearPath = $this->clearSlashes($path);

        [$controller, $action] = explode('/', $clearPath);
        $controller = ucfirst($controller).'Controller';

        if (!$action) $action = 'index';

        return [$this->findControllerPath($controller), $action];

    }

    /**
     * @param $path
     * @return array
     */
    private function slashVariants($path)
    {
        $path = $this->clearSlashes($path);
        return [$path, '/'.$path, $path.'/', '/'.$path.'/'];
    }

    /**
     * @param $path
     * @return false|string
     */
    private function clearSlashes($path)
    {
        if ($path[0] === '/')
            $path = substr($path, 1);
        if (substr($path, -1) === '/')
            $path = substr($path, 0, -1);

        return $path;
    }

    /**
     * @param $name
     * @return array|bool
     */
    private function parseRoute($name)
    {
        if (is_string($name))
            return explode('@', $name);
        elseif (!is_array($name))
            return false;
        elseif (is_string($name[0]))
            return explode('@', $name[0]);

        return false;
    }

    /**
     * @param $controller
     * @return string
     */
    private function findControllerPath($controller): ?string
    {
        $dir = self::baseDir()._DS.'controllers'._DS;
        $idir = new \DirectoryIterator($dir);

        $namespace = [];
        $controllerName = $controller.'.php';
        foreach ($idir as $file) {
            if (!$file->isDot() && $file->getFilename() === $controllerName) {
                $path = explode('controllers', $file->getPath());
                $namespace[] = 'Controllers';
                if (isset($path[1])) {
                    $dirs = explode(_DS, $path[1]);
                    foreach ($dirs as $item)
                        $namespace[] = $item;
                }
                $namespace[] = $controller;

                return str_replace('\\\\', '\\', implode("\\", $namespace));
            }
        }
        return  null;
    }
}
