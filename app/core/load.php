<?php
namespace app\core;

use ReflectionClass;
use Exception;

class LoadException extends Exception
{}

class Load
{
    public function __construct()
    {}

    public function view($view, $data = null)
    {
        $response = new Response($data);
        $file = 'app/views/' . mb_strtolower($view) . '.php';
        if (!file_exists($file)) {
            throw new LoadException(sprintf('View: %s does not exist', $view));
        }
        ob_start();
        require($file);
        unset($file);
        unset($view);
        return ob_get_clean();
    }

    public function json($jsonData, $errorMessage = null)
    {
        $result = array();

        if ($errorMessage) {
            $result['success'] = false;
            $result['errorMessage'] = $errorMessage;
        }
        else {
            $result['success'] = true;
            $result['data'] = $jsonData;
        }
        header('Content-Type: application/json');
        return json_encode($result);
    }

    public function jsonException(Exception $ex)
    {
        header('Content-Type: application/json');
        $result = array(
            'success' => false,
            'error' => array(
                'code' => $ex->getCode(),
                'message' => $ex->getMessage()
            )
        );
        return json_encode($result);
    }
}
