<?php declare(strict_types=1);
namespace app\core;

class RouteException extends CoreException
{}

class Route
{
    public static function dispatch()
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
        call_user_func_array(array(new $class(), $method), $params);
    }
}
