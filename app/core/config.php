<?php declare(strict_types=1);
namespace app\core;

use Exception;

class ConfigException extends Exception
{}

abstract class Config
{
    protected $config = array();

    public function __construct()
    {}

    public function get($key)
    {
        if (!isset($this->config[$key])) {
            throw new ConfigException('The specific key does not exist in the config file: ' . $key);
        }
        return $this->config[$key];
    }

    public function add($key, $value)
    {
        if (isset($this->config[$key])) {
            throw new ConfigException(
                'The specific key already exists in the config file: ' . $key . '. Use override function instead!'
            );
        }
        $this->config[$key] = $value;
    }

    public function override($key, $value)
    {
        $this->config[$key] = $value;
    }
}
