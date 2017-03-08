<?php declare(strict_types=1);
namespace core;

class CSRFException extends CoreException
{}

class CSRF
{
    private static $name = '_reserved_csrf_token';

    public static function generate()
    {
        $token = bin2hex(random_bytes(32));
        Session::set(self::$name, $token);
        return $token;
    }

    public static function name()
    {
        return self::$name;
    }

    public static function isset()
    {
        return Session::isset(self::$name);
    }

    public static function token()
    {
        return '<input type="hidden" name="' . self::$name . '" value="' . self::generate() . '">';
    }

    public static function check($token)
    {
        try {
            $savedToken = Session::get(self::$name);
            if ($savedToken != $token) {
                throw new CSRFException('CSRF failed due to unmatching tokens');
            }
            Session::remove(self::$name);
        }
        catch (SessionException $e) {
            throw new CSRFException(
                'Failed to get the CSRF token since it was never set, with message: ' . $e->getMessage()
                , $e->getCode(),
                $e
            );
        }
    }
}
