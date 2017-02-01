<?php
namespace app\core;

class Request
{
    private $requestType = null;
    private $args = array();

    public function __construct($requestType, array $args)
    {
        $this->requestType = $requestType;
        $this->args = $args;
    }

    public function getArgs()
    {
        return $this->args;
    }

    public function getType()
    {
        return $this->requestType;
    }
}
