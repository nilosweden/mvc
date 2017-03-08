<?php declare(strict_types=1);
namespace core;

use ErrorException;
use TypeError;
use Exception;

class CoreException extends Exception
{}

class Bootstrap
{
    public static function init($callback)
    {
        set_error_handler('core\Bootstrap::errorHandler');
        self::autoload();

        Session::init();
        require('global.php');
        require('lib/autoload.php');
        if ($callback !== null && is_callable($callback, false)) {
            $callback();
        }
        Route::dispatch();
    }

    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if (error_reporting() === 0) {
            return false;
        }
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    private static function autoload()
    {
        spl_autoload_register(function($className) {
            $className = mb_strtolower(str_replace('\\', '/', $className));
            $file = dirname(__DIR__) . '/' . $className . '.php';
            if (is_readable($file)) {
                require_once($file);
            }
        });
    }
}
