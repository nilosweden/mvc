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

        $class = '\app\controllers\\' . $controller;
        $controllerObj = new $class();

        $requestObj = new Request($_SERVER['REQUEST_METHOD'], $params);
        $controllerObj->setRequest($requestObj);
        $controllerObj->setSession($sessionObj);
        call_user_func_array(array($controllerObj, $method), $params);
    }
}
