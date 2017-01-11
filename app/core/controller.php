<?php
namespace app\core;

use Exception;

class ControllerException extends Exception
{}

abstract class Controller
{
    protected $request = null;
    protected $load = null;
    protected $session = null;

    public function __construct()
    {
        $this->load = new Load();
    }

    public function index()
    {
        throw new ControllerException('You should override the index function in your controller');
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function setSession(Session $session)
    {
        $this->session = $session;
    }
}
