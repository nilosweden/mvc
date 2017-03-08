<?php declare(strict_types=1);
namespace app\controllers;

use app\core\Controller as Controller;
use Throwable;

class Error extends Controller
{
    public function index()
    {
        echo $this->load->view(
            'error/index',
            new \Exception("This is a test exception to show you how the error page looks")
        );
    }

    public function viewException(Throwable $exception)
    {
        echo $this->load->view('error/index', $exception);
    }
}
