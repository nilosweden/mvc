<?php

namespace Database;

use PDO;

class ConnectionHandler
{
    private $connections = array();

    public function __construct(array $connectionsFromConfig)
    {
        foreach($connectionsFromConfig as $section => $connections) {
            $this->connections[$section] = array();
            foreach($connections as $name => $connection)
            {
                $connection['name'] = $name;
                $connection['section'] = $section;
                $this->connections[$section][$name] = new Connection($connection);
            }
        }
    }

    public function getConnection($section, $name)
    {
        if (!isset($this->connections[$section][$name])) {
            throw new DatabaseException('The connection name you provided does not exist, check your config file.');
        }
        return $this->connections[$section][$name];
    }

    public function newConnection(array $config)
    {
        if (empty($config['section']) || empty($config['name'])) {
            throw new DatabaseException(
                'You need to provide a name and a section for your connection.'
            );
        }
        if (!empty($this->connections[$config['section']])
            && (array_key_exists($config['name'], $this->connections[$config['section']]))) {
            throw new DatabaseException(
                'The connection name you provided already exists, choose a unique name.'
            );
        }
        return $this->connections[$config['section']][$config['name']] = new Connection($config);
    }

    public function getConnections()
    {
        return $this->connections;
    }
}
