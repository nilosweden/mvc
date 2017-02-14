<?php declare(strict_types=1);
namespace app\controllers;

use app\core\Controller as Controller;

class Api extends Controller
{
    public function index()
    {
        $cls = new \app\models\ApiReflection($this);
        echo $this->load->json($cls->getMethods());
    }

    public function userApi()
    {
        $reflection = new \app\models\ApiReflection("\app\controllers\UserApi");
        echo $this->load->json($reflection->getMethods());
    }
}
