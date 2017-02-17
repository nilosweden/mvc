<?php declare(strict_types=1);
namespace app\core;

use Exception;
use ReflectionClass;

class ParserException extends Exception
{}

class Parser
{
    private $unparsedArguments = '';
    private $controller = 'index';
    private $method = 'index';
    private $params = [];
    private $methodParameters = [];

    public function __construct()
    {
        $this->parse();
    }

    private function parse()
    {
        $this->parseUrl();
        $this->getParametersForMethod();

        $method = $_SERVER['REQUEST_METHOD'] ?? '';
        switch($method)
        {
            case 'POST':
                $this->parsePostArguments();
                break;
            case 'PUT':
                $this->parsePutAndDeleteArguments();
                break;
            case 'DELETE':
                $this->parsePutAndDeleteArguments();
                break;
            case 'GET':
                $this->parseGetArguments();
                break;
            default:
                throw new ParserException(
                    'The request type "' . $method . '" is not supported'
                );
        }
        $this->validateArguments();
    }

    public function getParameters() : array
    {
        return $this->params;
    }

    public function getMethod() : string
    {
        return $this->method;
    }

    public function getController() : string
    {
        return $this->controller;
    }

    public function getMethodParameters() : array
    {
        return $this->methodParameters;
    }

    private function parseUrl()
    {
        if (!empty($_GET['_reserved_url'])) {
            $parsedUrl = explode('/', $_GET['_reserved_url']);
            if (!empty($parsedUrl[0])) {
                $this->controller = $parsedUrl[0];
                unset($parsedUrl[0]);
                if (!empty($parsedUrl[1])) {
                    $this->method = $parsedUrl[1];
                    unset($parsedUrl[1]);
                }
            }
            if (!empty($parsedUrl[2])) {
                $this->unparsedArguments = implode('/', $parsedUrl);
            }
            unset($_GET['_reserved_url']);
        }
    }

    private static function parseJson(string $data, $toArray=false)
    {
        $json = json_decode($data, $toArray);
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
            throw new ParserException($message);
        }
        return $json;
    }

    private function getParametersForMethod()
    {
        $file = 'app/controllers/' . mb_strtolower($this->controller) . '.php';
        if (!file_exists($file)) {
            throw new RouteException('Controller "' . $this->controller . '" does not exist');
        }

        $ref = new ReflectionClass('\app\controllers\\' . $this->controller);
        if (!$ref->hasMethod($this->method)) {
            throw new RouteException('Method "' . $this->method . '" does not exist');
        }

        $params = $ref->getMethod($this->method)->getParameters();
        foreach ($params as $param) {
            $this->methodParameters[] = [
                "name" => $param->getName(),
                "type" => (string)$param->getType(),
                "optional" => $param->isOptional()
            ];
        }
    }

    private function parsePostArguments()
    {
        if (!empty($this->unparsedArguments)) {
            throw new RouteException(
                'You are not allowed to use multiple request types. Either send args as named arguments'
                . ' or with slashes, but not both'
            );
        }
        foreach ($this->methodParameters as $index => $param) {
            $this->params[$index] = rawurldecode($_POST[$param['name']] ?? '');
        }
    }

    private function parsePutAndDeleteArguments()
    {
        if (!empty($this->unparsedArguments)) {
            throw new RouteException(
                'You are not allowed to use multiple request types. Either send args as named arguments'
                . ' or with slashes, but not both'
            );
        }
        parse_str(file_get_contents('php://input'), $vars);
        foreach ($this->methodParameters as $index => $param) {
            $this->params[$index] = rawurldecode($vars[$param['name']] ?? '');
        }
    }

    private function parseGetArguments()
    {
        if (empty($_GET)) {
            $this->params = explode('/', parse_url($this->unparsedArguments, PHP_URL_PATH));
            foreach ($this->methodParameters as $index => $param) {
                $this->params[$index] = rawurldecode($this->params[$index]);
            }
        }
        else {
            if (!empty($this->unparsedArguments)) {
                throw new RouteException(
                    'You are not allowed to use multiple request types. Either send args as named arguments'
                    . ' or with slashes, but not both'
                );
            }
            foreach ($this->methodParameters as $index => $param) {
                $this->params[$index] = rawurldecode($_GET[$param['name']]) ?? null;
            }
        }
    }

    private function validateArguments()
    {
        foreach ($this->methodParameters as $index => $param) {
            if (empty($this->params[$index])) {
                throw new ParserException(
                    'You are missing parameter "' . $param['name'] . '" in for method ' . $this->method . '()'
                );
            }

            if ($param['type']) {
                if ($param['type'] === 'stdClass') {
                    try {
                        $this->params[$index] = (object)self::parseJson($this->params[$index], false);
                    }
                    catch (ParserException $e) {
                        throw new ParserException(
                            'Failed to convert json to type "' . $param['type'] . '" for parameter "'
                            . $param['name'] . '" with message: ' . $e->getMessage()
                        );
                    }
                }
                elseif ($param['type'] === 'array') {
                    try {
                        $this->params[$index] = (array)self::parseJson($this->params[$index], true);
                    }
                    catch (ParserException $e) {
                        throw new ParserException(
                            'Failed to convert json to type "' . $param['type'] . '" for parameter "'
                            . $param['name'] . '" with message: ' . $e->getMessage()
                        );
                    }
                }
                else if (!@settype($this->params[$index], $param['type'])) {
                    throw new ParserException(
                        'You cannot cast parameter to "' . $param['type'] . '" when calling method "'
                        . $this->method . '"'
                    );
                }
            }
        }
    }
}
