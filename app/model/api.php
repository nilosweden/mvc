<?php declare(strict_types=1);
namespace app\model;

use ReflectionClass;
use ReflectionMethod;

class Api
{
    public static function getMethods($class, array $excludeMethods = [])
    {
        $apiMethods = [];
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
                $apiMethods[$index] = array(
                    'name' => $methodName,
                    'comment' => $methodComment
                );
                foreach ($methodParams as $param) {
                    $apiMethods[$index]['params'][] = array(
                        'name' => $param->name,
                        'type' => (string)$param->getType(),
                        'optional' => $param->isOptional()
                    );
                }
            }
            ++$index;
        }
        return $apiMethods;
    }
}
