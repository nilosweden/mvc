<?php declare(strict_types=1);
namespace app\core;

use Exception;

class LoadException extends CoreException
{}

class Load
{
    private $session = null;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function view($view, $data = null)
    {
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
