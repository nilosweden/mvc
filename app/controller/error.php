<?php declare(strict_types=1);
namespace app\controller;

use core\Controller as Controller;
use core\View as View;
use Exception;

class TestException extends Exception
{}

class Error extends Controller
{
    public function index()
    {
        echo View::page(
            'error/index',
            new TestException("This is a test exception to show you how the error page looks")
        );
    }

    public function viewException(Exception $exception)
    {
        echo View::page('error/index', $exception);
    }
}
