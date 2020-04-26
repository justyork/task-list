<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 25.04.2020
 */

namespace Core;


class Core
{
    use Response;

    protected $layout = 'main';
    protected $request;
    protected $auth;

    public function __construct()
    {
        $this->request = new Request();
        $this->auth = new Authorization();
    }

    /**
     * @param $view
     * @param array $params
     * @return View
     */
    protected function render($view, $params = [])
    {
        $content = $this->renderPart($view, $params);

        if ($this->layout)
            return (new View('layouts'._DS.$this->layout, ['content' => $content]))->run();
        else
            return $content;
    }

    /**
     * @param $view
     * @param array $params
     * @param bool $output
     * @return string|void
     */
    protected function renderPart($view, $params = [], $output = false)
    {
        $data = (new View($view, $params))->run();
        if ($output) {
            echo $data;
            return;
        }

        return $data;
    }

    protected function redirect($path): void
    {
        header('Location: '.$path);
    }

    protected function refresh(): void
    {
        $this->redirect($_SERVER['HTTP_REFERER']);
    }


    protected function goBack()
    {
        $this->redirect($_SERVER['HTTP_REFERER']);
    }



}
