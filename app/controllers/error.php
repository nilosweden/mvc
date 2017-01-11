<?php
namespace app\controllers;

use app\core\Controller as Controller;

class Error extends Controller
{
    public function index()
    {
        echo $this->load->view(
            'error/index',
            array(
                'message' => 'This is the default error page',
                'page' => 'Example controller or method'
            )
        );
    }

    public function controllerNotFound($content)
    {
        $error = json_decode(base64url_decode($content), true);
        echo $this->load->view('error/index', $error);
    }

    public function methodNotFound($content)
    {
        $error = json_decode(base64url_decode($content), true);
        echo $this->load->view('error/index', $error);
    }

    public function unsupportedHttpMethod($content)
    {
        $error = json_decode(base64url_decode($content), true);
        echo $this->load->json(array(
            'The following request type: ' . $error['method'] . 'is not supported'
        ));
    }

    public function tooManyArguments($content)
    {
        $error = json_decode(base64url_decode($content), true);
        echo $this->load->view('error/toomanyarguments', $error);
    }

    public function missingArgument($content)
    {
        $error = json_decode(base64url_decode($content), true);
        echo $this->load->view('error/missingargument', $error);
    }

    public function multipleRequestMethods($content)
    {
        $error = json_decode(base64url_decode($content), true);
        echo $this->load->view(
            'error/multiplerequestmethods',
            $error
        );
    }

    public function catchableError($content)
    {
        $error = json_decode(base64url_decode($content), true);
        echo $this->load->view('error/catchable', $error);
    }

    public function parseUrl($content)
    {
        $error = json_decode(base64url_decode($content), true);
        echo $this->load->view('error/parseurl', $error);
    }
}
