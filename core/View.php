<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 25.04.2020
 */

namespace Core;


class View
{

    protected $path;
    protected $params;
    private $startOutput = false;

    public function __construct($view, $params = [])
    {
        $this->path = $this->getRealPath($view);
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function run()
    {
        return $this->collectContent();
    }


    /**
     * @return string
     */
    protected function render(string $view, array $params = []): string
    {
        return (new View($view, $params))->run();
    }

    /**
     * @param $view
     * @return string
     */
    private function getRealPath($view)
    {
        $delimeter = '/';
        if (strpos($view, '.') !== false)
            $delimeter = '.';

        $items = explode($delimeter, $view);

        $viewPath = Config::get('app.view.directory', 'views');

        if ($viewPath[0] !== _DS)
            $viewPath = _DS . $viewPath;

        return $viewPath . _DS . implode(_DS, $items) . '.php';
    }

    /**
     * @return string
     */
    private function collectContent(): string
    {

        $this->startOutput = true;
        $fullPath = Application::baseDir().$this->path;
        ob_start();
        // Инициализировать переменные
        foreach ($this->params as $key => $value) {
            $$key = $value;
        }

        require($fullPath);

        $content = ob_get_contents();
        ob_get_clean();
        $this->startOutput = false;

        return $content;
    }

}
