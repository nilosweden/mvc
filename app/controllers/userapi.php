<?php declare(strict_types=1);
namespace app\controllers;

use core\Controller as Controller;

class UserApi extends Controller
{
    public function index()
    {
        $cls = new \app\models\ApiReflection($this);
        echo $this->load->json($cls->getMethods());
    }

    public function add(array $arg1, array $arg2, $token)
    {
        echo $this->load->json([$this->session->get("token"), $token, $arg1, $arg2]);
    }

    public function remove($username)
    {
        echo $this->load->json($username);
    }
}
