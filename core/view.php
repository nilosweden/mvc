<?php declare(strict_types=1);
namespace core;

class ViewException extends CoreException
{}

class View
{
    public static function page($view, $data = null)
    {
        $file = 'app/view/' . mb_strtolower($view) . '.php';
        if (!file_exists($file)) {
            throw new ViewException('View does not exist: ' . $view);
        }
        ob_start();
        require($file);
        unset($file);
        unset($view);
        return ob_get_clean();
    }

    public static function json($jsonData, $errorMessage = null)
    {
        $result = array();

        if ($errorMessage) {
            $result['success'] = false;
            $result['message'] = $errorMessage;
        }
        else {
            $result['success'] = true;
            $result['data'] = $jsonData;
        }
        header('Content-Type: application/json');
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}
