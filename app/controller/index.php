<?php declare(strict_types=1);
namespace app\controller;

use core\Controller as Controller;
use core\View as View;

class Index extends Controller
{
    public function index()
    {
        echo View::page('index/index');
    }
}
