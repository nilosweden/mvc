<?php declare(strict_types=1);
namespace app\controller;

use core\Controller as Controller;
use core\View as View;
use core\Session as Session;
use \app\model\Api as ApiModel;

class UserApi extends Controller
{
    public function index()
    {
        $methods = ApiModel::getMethods($this);
        echo View::json($methods);
    }

    public function add(array $arg1, array $arg2)
    {
        echo View::json([$arg1, $arg2]);
    }

    public function remove($username = null)
    {
        echo View::json($username);
    }
}
