<?php declare(strict_types=1);
namespace app\controller;

use core\Controller as Controller;
use core\View as View;
use core\Session as Session;

class UserApi extends Controller
{
    public function index()
    {
        $cls = new \app\model\Api($this);
        echo View::json($cls->getMethods());
    }

    public function add(array $arg1, array $arg2)
    {
        echo View::json([Session::get('test'), $arg1, $arg2]);
    }

    public function remove($username)
    {
        echo View::json($username);
    }
}
