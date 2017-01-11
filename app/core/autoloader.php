<?php
namespace app\core;

class Autoloader
{
    public static function register()
    {
        spl_autoload_register(function($className) {
            $ds = DIRECTORY_SEPARATOR;
            $dir = dirname(dirname(__DIR__));
            $className = mb_strtolower(str_replace('\\', $ds, $className));
            $file = "{$dir}{$ds}{$className}.php";
            if (is_readable($file)) {
                require_once($file);
            }
        });
    }
}
