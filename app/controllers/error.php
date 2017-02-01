<?php
namespace app\controllers;

use app\core\Controller as Controller;
use app\core\Response as Response;

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

    public function controllerNotFound($content)
    {
        echo $this->load->view('error/index', $content);
    }

    public function methodNotFound($content)
    {
        echo $this->load->view('error/index', $content);
    }

    public function unsupportedHttpMethod($content)
    {
        echo $this->load->json($content);
    }

    public function tooManyArguments($content)
    {
        echo $this->load->view('error/toomanyarguments', $content);
    }

    public function missingArgument($content)
    {
        echo $this->load->view('error/missingargument', $content);
    }

    public function multipleRequestMethods($content)
    {
        echo $this->load->view(
            'error/multiplerequestmethods',
            $content
        );
    }

    public function catchableError($content)
    {
        echo $this->load->view('error/catchable', $content);
    }

    public function parseUrl($content)
    {
        echo $this->load->view('error/parseurl', $content);
    }
}
