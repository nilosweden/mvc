<?php declare(strict_types=1);
namespace app\core;

use Exception;

class SessionException extends Exception
{}

class Session
{
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(random_bytes(32));
        }
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
