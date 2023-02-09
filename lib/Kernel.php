<?php

namespace Lib;

class Kernel
{

    private $viewData = [];


    public function loadClass($class)
    {
        $class = str_replace("\\", DIRECTORY_SEPARATOR, $class);
        $aClass = explode(DIRECTORY_SEPARATOR, $class);
        foreach ($aClass as $key => $value) {
            if ($aClass[$key] != end($aClass)) {
                $aClass[$key] = strtolower($value);
            }
        }
        $class = implode(DIRECTORY_SEPARATOR, $aClass);

        if (is_file(ABSOLUTE_ROOT_PATH . DIRECTORY_SEPARATOR . $class . '.php'))
            require_once ABSOLUTE_ROOT_PATH . DIRECTORY_SEPARATOR . $class . '.php';

        if (is_file(ABSOLUTE_ROOT_PATH . $class . '.php'))
            require_once ABSOLUTE_ROOT_PATH . $class . '.php';
    }

    public function bootstrap()
    {
        spl_autoload_register([$this, "loadClass"]);
    }

    public function run()
    {

        $router = new \Lib\Router();
        $this->viewData["router"] = $router;


        if (isset($_SERVER["PATH_INFO"])) {
            $requestPath = $_SERVER["PATH_INFO"];
        } else {
            $requestPath = '/';
        }

        $requestRoute = $router->getRoute($requestPath);

        $controllerName = "App\Controllers\\" . $requestRoute["controller"] . "Controller";

        $methodName = $requestRoute["method"];

        $controller = new $controllerName($router);

        if (method_exists($controller, $methodName)) {
            $this->viewData = array_merge(
                $this->viewData,
                (array)$controller->$methodName()
            );
            $this->renderResponse();
        } else {
            die("methode " . $methodName . " inconnue");
        }
    }

    public function renderResponse()
    {
        extract($this->viewData, EXTR_OVERWRITE);
        if (isset($redirect)) {
            $redirectUrl = $router->generateUrl($redirect["routeName"], $redirect["parameters"]);
            header("Location:" . $redirectUrl);
            exit();
        } elseif (isset($template)) {
            $messages = new \Lib\Session("flashbag");
            $user = new \Lib\Session("user");
            $env = new \Lib\EnvLoader();
            $templatePath = "www/views/" . $template;

            if (isset($_raw) && $_raw) {
                include $templatePath;
            } else {
                include 'www/views/layout.phtml';
            }
        }
    }
}
