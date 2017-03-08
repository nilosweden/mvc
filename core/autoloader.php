<?php declare(strict_types=1);
namespace core;

class Autoloader
{
    public static function register()
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
