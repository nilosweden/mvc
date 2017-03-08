<?php declare(strict_types=1);
namespace app\controller;

use core\Controller as Controller;
use core\View as View;

class Api extends Controller
{
    public function index()
    {
        $cls = new \app\model\Api($this);
        echo View::json($cls->getMethods());
    }

    public function userApi()
    {
        $cls = new \app\model\Api("\app\controller\UserApi");
        echo View::json($cls->getMethods());
    }
}
