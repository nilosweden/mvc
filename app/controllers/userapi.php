<?php declare(strict_types=1);
namespace app\controllers;

use app\core\Controller as Controller;

class UserApi extends Controller
{
    public function index()
    {
        $cls = new \app\models\ApiReflection($this);
        echo $this->load->json($cls->getMethods());
    }

    public function add(array $arg1 = null, array $arg2)
    {
        echo $this->load->json([$this->request->getType(), $arg1, $arg2]);
    }

    public function remove($username)
    {
        echo $this->load->json("ok");
    }
}
