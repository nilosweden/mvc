<?php
namespace app\core;

use ReflectionMethod;

class Route
{
    protected $controller;
    protected $method;
    protected $params = array();
    protected $controllerObj = null;

    public function __construct($defaultController = 'index', $defaultMethod = 'index')
    {
        set_error_handler('app\core\Route::myErrorHandler');
        $this->controller = $defaultController;
        $this->method = $defaultMethod;
    }

    public function myErrorHandler($errno, $errstr, $errfile, $errline)
    {
        if (E_RECOVERABLE_ERROR === $errno) {
            Route::showErrorPage('catchableError', array(
                'errstr' => $errstr,
                'errfile' => $errfile,
                'errline' => $errline
            ));
        }
        return false;
    }

    public function handleRequest(Session $sessionObj)
    {
        $url = isset($_GET['url']) ? $_GET['url'] : '';
        $unparsedArguments = null;
        $this->parseUrlAndSetController($url, $unparsedArguments);
        $file = 'app/controllers/' . $this->controller . '.php';
        if (!file_exists($file)) {
            $this->showErrorPage('controllerNotFound', array(
                'method' => $this->controller,
                'message' => 'Controller not found'
            ));
        }
        $class = 'app\controllers\\' . $this->controller;
        $this->controllerObj = new $class();
        if (!method_exists($this->controllerObj, $this->method)) {
            $this->showErrorPage('methodNotFound', array(
                'method' => $this->method,
                'message' => 'Method not found'
            ));
        }
        $refObj = new ReflectionMethod($class, $this->method);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePostRequest($unparsedArguments, $refObj);
        }
        else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->handleGetRequest($unparsedArguments, $refObj);
        }
        else {
            $this->showErrorPage('unsupportedHttpMethod', array(
                'method' => $_SERVER['REQUEST_METHOD']
            ));
        }

        // Set session, config and request
        $requestObj = new Request($_SERVER['REQUEST_METHOD'], $this->params);
        $this->controllerObj->setRequest($requestObj);
        $this->controllerObj->setSession($sessionObj);
        call_user_func_array(array($this->controllerObj, $this->method), $this->params);
    }

    private function handlePostRequest($unparsedArguments, &$refObj)
    {
        if ($unparsedArguments) {
            $this->showErrorPage('multipleRequestMethods');
        }
        $refParams = $refObj->getParameters();
        foreach ($refParams as $param) {
            $name = $param->getName();
            $optional = $param->isOptional();
            if (empty($_POST[$name]) && $optional==false) {
                $this->showErrorPage('missingArgument', array(
                    'argument' => $name
                ));
            }
            if (isset($_POST[$name])) {
                $unparsedArguments .= $_POST[$name] . '/';
                unset($_POST[$name]);
            }
        }
        if ($_POST) {
            $this->showErrorPage('tooManyArguments', array(
                'arguments' => $_POST
            ));
        }
        $this->parseArguments($unparsedArguments);
    }

    private function handleGetRequest($unparsedArguments, &$refObj)
    {
        if (isset($_GET['url'])) {
            unset($_GET['url']);
        }
        if (!$unparsedArguments) {
            unset($_GET['url']);
            $refParams = $refObj->getParameters();
            foreach ($refParams as $param) {
                $name = $param->getName();
                $optional = $param->isOptional();
                if (empty($_GET[$name]) && $optional==false) {
                    $this->showErrorPage('missingArgument', array(
                        'argument' => $name
                    ));
                }
                if (isset($_GET[$name])) {
                    $unparsedArguments .= $_GET[$name] . '/';
                    unset($_GET[$name]);
                }
            }
            if ($_GET) {
                $this->showErrorPage('tooManyArguments', array(
                    'arguments' => $_GET
                ));
            }
            $this->parseArguments($unparsedArguments);
        }
        else {
            if ($_GET) {
                if ($unparsedArguments) {
                    $this->showErrorPage('multipleRequestMethods');
                }
            }
            $this->parseArguments($unparsedArguments);
            $numArgsPassed = sizeof($this->params);
            $refParams = $refObj->getParameters();
            foreach ($refParams as $param) {
                if (!$param->isOptional()) {
                    --$numArgsPassed;
                }
                if ($numArgsPassed < 0) {
                    $this->showErrorPage('missingargument', array('argument' => $param->getName()));
                }
            }
        }
    }

    private function parseUrlAndSetController($url, &$unparsedArguments)
    {
        preg_match_all('/\//', $url, $matches, PREG_OFFSET_CAPTURE);
        $parsedUrl = array();

        if (sizeof($matches[0]) > 1) {
            $matchPos = $matches[0][1][1];
            $partialUrl = mb_substr($url, 0, $matchPos);
            $parsedUrl = explode('/', $partialUrl);
            $arguments = mb_substr($url, $matchPos);
            $unparsedArguments = ($arguments == '/') ? null : mb_substr($arguments, 1);
        }
        else {
            $parsedUrl = explode('/', $url);
        }
        if (!empty($parsedUrl[0])) {
            $this->controller = $parsedUrl[0];
            if (!empty($parsedUrl[1])) {
                $this->method = $parsedUrl[1];
            }
        }
    }

    private function parseArguments($arguments)
    {
        $parsedArgs = array();
        $closingChar = array(
            '{' => '}',
            '[' => ']'
        );
        while ($arguments) {
            if (!empty($closingChar[$arguments[0]])) {
                $firstChar = $arguments[0];
                $posEnding = strpos($arguments, $closingChar[$firstChar] . '/');

                if ($posEnding === false) {
                    $posEnding = strlen($arguments);
                    $lastChar = mb_substr($arguments, -1);
                }
                else {
                    $lastChar = $arguments[$posEnding];
                }

                if ($lastChar == $closingChar[$firstChar]) {
                    $parsedArgs[] = $this->parseJson(mb_substr($arguments, 0, $posEnding + 1));
                    $arguments = mb_substr($arguments, $posEnding + 2);
                }
                else {
                    $posEnding = strpos($arguments, $closingChar[$firstChar] . '/');
                    if ($posEnding === false) {
                        $this->showErrorPage('parseUrl', array(
                            'arguments' => $arguments
                        ));
                    }
                    $parsedArgs[] = $this->parseJson(mb_substr($arguments, 0, $posEnding + 1));
                    $arguments = mb_substr($arguments, $posEnding+2);
                }
            }
            else {
                $posEnding = strpos($arguments, '/');
                if ($posEnding !== false) {
                    $parsedArgs[] = mb_substr($arguments, 0, $posEnding);
                    $arguments = mb_substr($arguments, $posEnding + 1);
                }
                else {
                    $parsedArgs[] = $arguments;
                    $arguments = null;
                    break;
                }
            }
        }
        $this->params = $parsedArgs;
    }

    private function parseJson($data)
    {
        $json = json_decode($data, true);
        if (json_last_error()) {
            $message = null;
            switch (json_last_error()) {
                case JSON_ERROR_DEPTH:
                    $message = 'Maximum stack depth exceeded';
                break;
                case JSON_ERROR_STATE_MISMATCH:
                    $message = 'Underflow or the modes mismatch';
                break;
                case JSON_ERROR_CTRL_CHAR:
                    $message = 'Unexpected control character found';
                break;
                case JSON_ERROR_SYNTAX:
                    $message = 'Syntax error, malformed JSON';
                break;
                case JSON_ERROR_UTF8:
                    $message = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
                default:
                    $message = 'Unknown error';
                break;
            }
            $this->showErrorPage('parseUrl', array(
                'arguments' => $data,
                'message' => $message
            ));
        }
        return $json;
    }

    private function showErrorPage($method, array $userData = array())
    {
        $errorData = array(
            'data' => $userData,
            'info' => array(
                'url' => $_SERVER['REQUEST_URI'],
                'method' => $_SERVER['REQUEST_METHOD']
            )
        );
        $content = base64url_encode(json_encode($errorData));
        header('Location: /mvc/error/' . $method . '/' . $content);
        die();
    }
}
