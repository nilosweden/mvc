<?php

spl_autoload_register(function($class) {
    $file = str_replace('\\','/',dirname(__DIR__) . "/" . mb_strtolower($class) . ".class.php");
    if (is_readable($file)) {
        require($file);
    }
});
