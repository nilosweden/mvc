<?php

namespace Database;

use PDO;
use PDOException;

class Connection
{
    private $properties = null;
    private $pdo = null;
    private $section = null;
    private $name = null;

    public function __construct($properties)
    {
        $this->properties = $properties;
        if (empty($this->properties['charset'])) {
            $this->properties['charset'] = 'utf8';
        }
        $this->connect();
    }

    private function connect()
    {
        if ($this->pdo) {
            throw new DatabaseException('This connection already exists and is connected.');
        }
        try {
            $pdo = new PDO(
                'mysql'
                    . ':host='
                    . $this->properties['host']
                    . ';dbname='
                    . $this->properties['database']
                    . ';charset='
                    . $this->properties['charset']
                    . ';port='
                    . $this->properties['port'],
                $this->properties['username'],
                $this->properties['password'],
                array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . $this->properties['charset'],
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                )
            );
            $this->pdo = $pdo;
        }
        catch(PDOException $e) {
            throw new DatabaseException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    public function getPDO()
    {
        return $this->pdo;
    }

    public function getProperties()
    {
        return $this->properties;
    }
}
