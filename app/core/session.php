<?php declare(strict_types=1);
namespace app\core;

use Exception;

class SessionException extends Exception
{}

class Session
{
    public function __construct()
    {
        session_start();
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function get($key)
    {
        if (!isset($_SESSION[$key])) {
            throw new SessionException('Specific key does not exist in session: ' . $key);
        }
        return $_SESSION[$key];
    }
}
