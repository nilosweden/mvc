<?php declare(strict_types=1);
namespace app\core;

use Exception;

class RouteException extends Exception
{}

class Route
{
    public static function dispatch(Session $sessionObj)
    {
        try {
            $parser = new Parser();
            $controller = $parser->getController();
            $method = $parser->getMethod();
            $params = $parser->getParameters();
        }
        catch (ParserException $e) {
            throw new RouteException($e->getMessage(), $e->getCode(), $e);
        }

        $file = 'app/controllers/' . mb_strtolower($controller) . '.php';
        if (!file_exists($file)) {
            throw new RouteException('Controller ' . $controller . ' does not exist');
        }

        $class = '\app\controllers\\' . $controller;
        $controllerObj = new $class();
        if (!method_exists($controllerObj, $method)) {
            throw new RouteException('Method ' . $method . ' does not exist');
        }

        $requestObj = new Request($_SERVER['REQUEST_METHOD'], $params);
        $controllerObj->setRequest($requestObj);
        $controllerObj->setSession($sessionObj);
        call_user_func_array(array($controllerObj, $method), $params);
    }
}
