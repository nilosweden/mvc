<?php
namespace app\controllers;

use app\core\Controller as Controller;

class UserApi extends Controller
{
    public function index()
    {
        $cls = new \app\models\ApiReflection($this);
        echo $this->load->json($cls->getMethods());
    }

    public function add($username)
    {
        echo $this->load->json("ok");
    }

    public function remove($username)
    {
        echo $this->load->json("ok");
    }
}
