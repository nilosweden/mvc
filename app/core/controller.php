<?php declare(strict_types=1);
namespace app\core;

use Exception;

class ControllerException extends Exception
{}

abstract class Controller
{
    protected $load = null;
    protected $session = null;

    public function __construct()
    {
        $this->session = new Session();
        $this->load = new Load($this->session);
    }

    public function index()
    {
        throw new ControllerException('You need to override the index() function in your controller');
    }
}
