<?php declare(strict_types=1);
namespace app\core;

class Response
{
    private $data = null;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}
