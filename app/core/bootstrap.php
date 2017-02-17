<?php declare(strict_types=1);
namespace app\core;

use app\core\Route as Route;
use app\core\Session as Session;
use ErrorException;
use TypeError;

class Bootstrap
{
    public static function init($callback = null)
    {
        require('app/core/global.php');
        require('app/core/autoloader.php');
        Autoloader::register();
        set_error_handler('app\Core\Bootstrap::errorHandler');

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
        catch (RouteException $e) {
            $error = new \app\controllers\error();
            $error->viewException($e);
        }
        catch (ControllerException $e) {
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
