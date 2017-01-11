<?php
namespace app\config;

use app\core\Config as Config;

class ServerConfig extends Config
{
    public function __construct()
    {
        $this->config['databases'] = array(
            'host2' => array(
                'default' => array(
                    'username' => 'admin',
                    'database' => 'users',
                    'driver' => 'mysql',
                    'prefix' => '',
                    'host' => 'hosttest2',
                    'collation' => 'utf8_general_ci',
                    'password' => 'admin',
                    'port' => 113322
                ),
                'readslave' => array(
                    'username' => 'readonly',
                    'prefix' => '',
                    'host' => '127.0.0.1',
                    'database' => 'users',
                    'collation' => 'utf8_general_ci',
                    'password' => 'readonly_password',
                    'driver' => 'mysql',
                    'port' => 113322
                )
            ),
            'default' => array(
                'default' => array(
                    'username' => 'admin',
                    'database' => 'somedb',
                    'driver' => 'mysql',
                    'prefix' => '',
                    'host' => 'somehost',
                    'collation' => 'utf8_general_ci',
                    'password' => 'admin',
                    'port' => 113322
                )
            )
        );
    }
}
