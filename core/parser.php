<?php declare(strict_types=1);
namespace core;

use ReflectionClass;

class ParserException extends CoreException
{}

class Parser
{
    private $unparsedArguments = '';
    private $controller = 'index';
    private $method = 'index';
    private $params = [];
    private $methodParameters = [];
    private $requestType = null;

    public function __construct()
    {
        $this->requestType = $_SERVER['REQUEST_METHOD'] ?? null;
        $this->parseUrl();
        $this->getParametersForMethod();
        $this->parseArguments();
        $this->validateArguments();
    }

    private function parseArguments()
    {
        switch($this->requestType)
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
                    'The request type "' . $this->requestType . '" is not supported'
                );
        }
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

    private function getMethodParameters() : array
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
        $file = 'app/controller/' . mb_strtolower($this->controller) . '.php';
        if (!file_exists($file)) {
            throw new ParserException('Controller "' . $this->controller . '" does not exist');
        }

        $ref = new ReflectionClass('\app\controller\\' . $this->controller);
        if (!$ref->hasMethod($this->method)) {
            throw new ParserException('Method "' . $this->method . '" does not exist');
        }

        $methodObj = $ref->getMethod($this->method);
        $comment = $methodObj->getDocComment();
        if ($comment) {
            $matches = [];
            preg_match('/@accept ([a-zA-Z ,]+)/', $comment, $matches);
            if (isset($matches[1])) {
                $requestMethods = explode(', ', $matches[1]);
                if (!in_array($this->requestType, $requestMethods)) {
                    throw new ParserException(
                        'Method "' . $this->method . '" does not accept a ' . $this->requestType . ' request'
                    );
                }
            }
        }

        $params = $methodObj->getParameters();
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
                $this->params[$index] = rawurldecode($this->params[$index] ?? '');
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
                $this->params[$index] = rawurldecode($_GET[$param['name']] ?? '');
            }
        }
    }

    private function validateArguments()
    {
        foreach ($this->methodParameters as $index => $param) {
            if ($param['optional'] == false && empty($this->params[$index])) {
                throw new ParserException(
                    'You are missing parameter "' . $param['name'] . '" for method ' . $this->method . '()'
                );
            }

            if ($param['type']) {
                if ($param['type'] === 'stdClass') {
                    try {
                        $this->params[$index] = (object)self::parseJson($this->params[$index], false);
                    }
                    catch (ParserException $e) {
                        throw new ParserException(
                            'Failed to convert json data to type "' . $param['type'] . '" for parameter "'
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
                            'Failed to convert json data to type "' . $param['type'] . '" for parameter "'
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
