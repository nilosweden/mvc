<?php
require_once('app/core/bootstrap.php');
use app\core\Bootstrap;
use Database\Database as DB;
use app\Config\ServerConfig as ServerConfig;

$bootstrap = new Bootstrap();
$bootstrap->init(function() {
    $serverConfig = new ServerConfig();
    DB::newConnectionHandler($serverConfig->get('databases'));
});
