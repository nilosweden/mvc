<?php
namespace app\config;

use app\core\Config as Config;

class ServerConfig extends Config
{
    public function __construct()
    {
        $this->config['databases'] = array(
            'default' => array(
                'default' => array(
                    'username' => 'root',
                    'database' => 'db',
                    'driver' => 'mysql',
                    'prefix' => '',
                    'host' => '127.0.0.1',
                    'collation' => 'utf8_general_ci',
                    'password' => 'passwd',
                    'port' => 3306
                )
            )
        );
    }
}
