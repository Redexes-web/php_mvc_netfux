<?php

namespace App\Controllers;

use Lib\Utils;

abstract class AbstractController
{
    protected $router;

    public function __construct($router = null)
    {
        $this->router = $router;
    }

    protected function render(string $template, array $parameters = [])
    {
        $render = $parameters;
        $render["template"] = $template;
        return $render;
    }
    protected function redirectToRoute(string $routeName, array $parameters = [])
    {
        $render = [];
        $render["redirect"]["routeName"] = $routeName;
        $render["redirect"]["parameters"] = $parameters;
        return $render;
    }
}
