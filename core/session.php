<?php declare(strict_types=1);
namespace core;

class SessionException extends CoreException
{}

class Session
{
    public static function init()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function newToken()
    {
        $_SESSION['token'] = bin2hex(random_bytes(32));
        return $_SESSION['token'];
    }

    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function get($key)
    {
        if (!isset($_SESSION[$key])) {
            throw new SessionException('Specific key does not exist in session: ' . $key);
        }
        return $_SESSION[$key];
    }
}
