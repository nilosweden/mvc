<?php declare(strict_types=1);
namespace core;

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

        $class = '\app\controller\\' . $controller;
        call_user_func_array(array(new $class(), $method), $params);
    }
}
