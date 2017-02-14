<?php declare(strict_types=1);
namespace app\controllers;

use app\core\Controller as Controller;
use app\core\Response as Response;
use Throwable;

class Error extends Controller
{
    public function index()
    {
        echo $this->load->view(
            'error/index',
            array(
                'data' => array(
                    'message' => 'This is the default error page',
                    'page' => 'Example controller or method',
                    'method' => 'test method'
                ),
                'info' => array(
                    'url' => 'example url',
                    'method' => 'GET'
                )
            )
        );
    }

    public function viewException(Throwable $exception)
    {
        echo $this->load->view('error/index', $exception);
    }
}
