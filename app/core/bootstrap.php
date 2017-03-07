<?php declare(strict_types=1);
namespace app\core;

use app\core\Route as Route;
use ErrorException;
use TypeError;
use Exception;

class CoreException extends Exception
{}

class Bootstrap
{
    public static function init($callback = null)
    {
        set_error_handler('app\Core\Bootstrap::errorHandler');
        require('app/core/global.php');
        require('app/core/autoloader.php');
        Autoloader::register();

        try {
            require('lib/autoload.php');
            if ($callback !== null) {
                $callback();
            }
            Route::dispatch();
        }
        catch (ErrorException $e) {
            $error = new \app\controllers\error();
            $error->viewException($e);
        }
        catch (TypeError $e) {
            $error = new \app\controllers\error();
            $error->viewException($e);
        }
        catch (CoreException $e) {
            $error = new \app\controllers\error();
            $error->viewException($e);
        }
    }

    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if (error_reporting() === 0) {
            return false;
        }
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}
