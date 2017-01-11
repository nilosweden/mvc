<?php
namespace app\models;

use ReflectionClass;
use ReflectionMethod;

class ApiReflection
{
    protected $apiMethods = array();

    public function __construct($class, array $excludeMethods = array())
    {
        $controllerExcludedMethods = array(
            '__construct',
            'index',
            'setRequest'
        );
        foreach ($controllerExcludedMethods as $method) {
            if (!in_array($method, $excludeMethods)) {
                $excludeMethods[] = $method;
            }
        }
        $reflectionClass = new ReflectionClass($class);
        $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
        $apiMethods = array();
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
