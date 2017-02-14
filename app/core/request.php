<?php declare(strict_types=1);
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

    public function getArguments() : array
    {
        return $this->args;
    }

    public function getType() : string
    {
        return $this->requestType;
    }
}
