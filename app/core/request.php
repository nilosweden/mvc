<?php
namespace app\core;

class Request
{
    private $requestType = null;
    private $parameters = array();

    public function __construct($requestType, array $parameters)
    {
        $this->requestType = $requestType;
        $this->parameters = $parameters;
    }

    public function getArgs()
    {
        return $this->parameters;
    }

    public function getType()
    {
        return $this->requestType;
    }
}
