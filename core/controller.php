<?php declare(strict_types=1);
namespace core;

use Exception;

class ControllerException extends CoreException
{}

abstract class Controller
{
    public function index()
    {
        throw new ControllerException('You need to override the index() function in your controller');
    }
}
