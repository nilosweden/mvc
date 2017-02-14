<?php declare(strict_types=1);
namespace app\models;

use ReflectionClass;
use ReflectionMethod;

class ApiReflection
{
    protected $apiMethods = [];

    public function __construct($class, array $excludeMethods = [])
    {
        if (is_subclass_of($class, '\app\core\Controller')) {
            $reflectionClass = new ReflectionClass('\app\core\Controller');
            $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $method) {
                if (!in_array($method, $excludeMethods)) {
                    $excludeMethods[] = $method->name;
                }
            }
        }
        $reflectionClass = new ReflectionClass($class);
        $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
        $index = 0;
        foreach ($methods as $method) {
            $methodParams = $method->getParameters();
            $methodName = $method->getName();
            $methodComment = $method->getDocComment();
            if (!in_array($methodName, $excludeMethods)) {
                $this->apiMethods[$index] = array(
                    'name' => $methodName,
                    'comment' => $methodComment
                );
                foreach ($methodParams as $param) {
                    $this->apiMethods[$index]['params'][] = array(
                        'name' => $param->name,
                        'optional' => $param->isOptional()
                    );
                }
            }
            ++$index;
        }
    }

    public function getMethods()
    {
        return $this->apiMethods;
    }
}
