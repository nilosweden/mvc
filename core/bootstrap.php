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
        require('global.php');
        require('autoloader.php');
        Autoloader::register();
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
}
