<?php declare(strict_types=1);
namespace app\controllers;

use app\core\Controller as Controller;

class Index extends Controller
{
    public function index()
    {
        echo $this->load->view('index/index');
    }
}
