<?php declare(strict_types=1);
namespace app\controller;

use core\Controller as Controller;
use core\View as View;
use \app\model\Api as ApiModel;

class Api extends Controller
{
    public function index()
    {
        $methods = ApiModel::getMethods($this);
        echo View::json($methods);
    }

    public function userApi()
    {
        $methods = ApiModel::getMethods('\app\controller\UserApi');
        echo View::json($methods);
    }
}
