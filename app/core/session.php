<?php declare(strict_types=1);
namespace app\core;

class SessionException extends CoreException
{}

class Session
{
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function generateToken()
    {
        $_SESSION['token'] = bin2hex(random_bytes(32));
        return $_SESSION['token'];
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
