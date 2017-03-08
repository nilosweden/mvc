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

    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function remove($key)
    {
        unset($_SESSION[$key]);
    }

    public static function isset($key)
    {
        if (!isset($_SESSION[$key])) {
            return false;
        }
        return true;
    }

    public static function get($key)
    {
        if (!isset($_SESSION[$key])) {
            throw new SessionException('Specific key does not exist in session: ' . $key);
        }
        return $_SESSION[$key];
    }
}
