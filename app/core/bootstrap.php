<?php
namespace app\core;

use app\core\Route as Route;
use app\core\Session as Session;

class Bootstrap
{
    public function __construct()
    {
        require('app/core/global.php');
        require('app/core/autoloader.php');
        Autoloader::register();
    }

    public function init($callback = null)
    {
        require('lib/autoload.php');

        if ($callback !== null) {
            $callback();
        }

        // Setup routing
        $sessionObj = new Session();
        $route = new Route();
        $route->handleRequest($sessionObj);
    }
}
