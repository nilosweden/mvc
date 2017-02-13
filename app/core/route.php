<?php
namespace app\core;

use ReflectionMethod;
use ReflectionParameter;

class Route
{
    protected $controller = null;
    protected $method = null;
    protected $params = array();
    protected $controllerObj = null;

    public function __construct($defaultController = 'index', $defaultMethod = 'index')
    {
        set_error_handler('app\core\Route::errorHandler');
        $this->controller = $defaultController;
        $this->method = $defaultMethod;
    }

    public static function errorHandler($errno, $errstr, $errfile, $errline)
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
        $file = 'app/controllers/' . mb_strtolower($this->controller) . '.php';
        if (!file_exists($file)) {
            $this->showErrorPage('controllerNotFound', array(
                'method' => $this->controller,
                'message' => 'Controller not found'
            ));
        }

        $class = '\app\controllers\\' . $this->controller;
        $this->controllerObj = new $class();
        if (!method_exists($this->controllerObj, $this->method)) {
            $this->showErrorPage('methodNotFound', array(
                'method' => $this->method,
                'message' => 'Method not found'
            ));
        }

        $refObj = new ReflectionMethod($class, $this->method);
        $refParams = $refObj->getParameters();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePostRequest($unparsedArguments, $refParams);
        }
        else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->handleGetRequest($unparsedArguments, $refParams);
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

    private function handlePostRequest($unparsedArguments, &$refParams)
    {
        if ($unparsedArguments) {
            $this->showErrorPage('multipleRequestMethods');
        }
        $methodArgumentHints = array();
        foreach ($refParams as $param) {
            $name = $param->getName();
            $methodArgumentHints[] = $this->getParameterTypeHinting($param);
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
        $this->parseArguments($unparsedArguments, $methodArgumentHints);
    }

    private function handleGetRequest(&$unparsedArguments, &$refParams)
    {
        if (isset($_GET['url'])) {
            unset($_GET['url']);
        }

        if (empty($unparsedArguments)) {
            $methodArgumentHints = array();
            foreach ($refParams as $param) {
                $name = $param->getName();
                $methodArgumentHints[] = $this->getParameterTypeHinting($param);
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

            $this->parseArguments($unparsedArguments, $methodArgumentHints);
        }
        else {
            if ($_GET) {
                if ($unparsedArguments) {
                    $this->showErrorPage('multipleRequestMethods');
                }
            }

            $methodArgumentHints = array();
            foreach ($refParams as $param) {
                $methodArgumentHints[] = $this->getParameterTypeHinting($param);
            }
            $this->parseArguments($unparsedArguments, $methodArgumentHints);
            $numArgsPassed = sizeof($this->params);

            foreach ($refParams as $param) {
                if (!$param->isOptional() || $numArgsPassed > 0) {
                    --$numArgsPassed;
                }
                if ($numArgsPassed < 0) {
                    $this->showErrorPage('missingargument', array('argument' => $param->getName()));
                }
            }

            if ($numArgsPassed) {
                $unnecessaryArgs = array();
                foreach ($this->params as $param) {
                    $unnecessaryArgs[$param] = $param;
                    echo $param . " " . $numArgsPassed . "<hr>";
                }
                $this->showErrorPage('tooManyArguments', array(
                    'arguments' => $unnecessaryArgs
                ));
            }
        }
    }

    private function getParameterTypeHinting(ReflectionParameter $param)
    {
        preg_match(
            '/\[ ([A-Z\\a-z0-9]+) \$/',
            $param->__toString(),
            $matches
        );
        if (isset($matches[1])) {
            return trim(str_replace('<required>', '', $matches[1]));
        }
        return null;
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

    private function parseArguments(&$unparsedArguments, &$methodArguments)
    {
        $parsedArgs = array();
        $closingChar = array(
            '{' => '}',
            '[' => ']'
        );
        $i = 0;
        while ($unparsedArguments != '') {
            if (!empty($methodArguments[$i]) && !empty($closingChar[$unparsedArguments[0]])) {
                $firstChar = $unparsedArguments[0];
                $posEnding = strpos($unparsedArguments, $closingChar[$firstChar] . '/');

                if ($posEnding === false) {
                    $posEnding = strlen($unparsedArguments);
                    $lastChar = mb_substr($unparsedArguments, -1);
                }
                else {
                    $lastChar = $unparsedArguments[$posEnding];
                }

                if ($lastChar == $closingChar[$firstChar]) {
                    $parsedArgs[] = $this->parseJson(mb_substr($unparsedArguments, 0, $posEnding + 1));
                    $unparsedArguments = mb_substr($unparsedArguments, $posEnding + 2);
                }
                else {
                    $posEnding = strpos($unparsedArguments, $closingChar[$firstChar] . '/');
                    if ($posEnding === false) {
                        $this->showErrorPage('parseUrl', array(
                            'arguments' => $unparsedArguments
                        ));
                    }
                    $parsedArgs[] = $this->parseJson(mb_substr($unparsedArguments, 0, $posEnding + 1));
                    $unparsedArguments = mb_substr($unparsedArguments, $posEnding+2);
                }
            }
            else {
                $posEnding = strpos($unparsedArguments, '/');
                if ($posEnding !== false) {
                    $parsedArgs[] = mb_substr($unparsedArguments, 0, $posEnding);
                    $unparsedArguments = mb_substr($unparsedArguments, $posEnding + 1);
                }
                else {
                    $parsedArgs[] = $unparsedArguments;
                    $unparsedArguments = null;
                    break;
                }
            }
            ++$i;
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

    private static function showErrorPage($method, array $userData = array())
    {
        $error = array(
            'data' => $userData,
            'info' => array(
                'url' => $_SERVER['REQUEST_URI'],
                'method' => $_SERVER['REQUEST_METHOD']
            )
        );
        $errorObject = new \app\controllers\error();
        $errorObject->{$method}($error);
        die();
    }
}
