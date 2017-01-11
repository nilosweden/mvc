<?php

namespace Database;

use PDO;
use PDOException;

class Query
{
    private $query = null;
    private $bindings = array();
    private $statement = null;
    private $connection = null;
    private $lastExecution = 0;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function query($query, array $bindings = array())
    {
        $this->query = $query;
        $this->bindings = $bindings;
        try {
            $this->statement = $this->connection->getPDO()->prepare($query);
            $time_start = microtime(true);

            if (count($bindings) > 0) {
                $this->statement->execute($bindings);
            }
            else {
                $this->statement->execute();
            }

            $time_end = microtime(true);
            $this->lastExecution = ($time_end - $time_start);
            return $this;
        }
        catch(PDOException $e) {
            throw new DatabaseException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    public function transaction()
    {
        $this->connection->getPDO()->beginTransaction();
        return $this;
    }

    public function commit()
    {
        $this->connection->getPDO()->commit();
        return $this;
    }

    public function rollBack()
    {
        $this->connection->getPDO()->rollBack();
        return $this;
    }

    public function execTime()
    {
        return $this->lastExecution;
    }

    public function lastInsertedId()
    {
        return $this->connection->getPDO()->lastInsertId();
    }

    public function fetchOne()
    {
        try {
            return $this->statement->fetch(PDO::FETCH_ASSOC);
        }
        catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    public function fetchObject($class)
    {
        try {
            return $this->statement->fetchObject($class);
        }
        catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    public function fetchColumn($columnNumber = 0)
    {
        try {
            return $this->statement->fetchColumn($columnNumber);
        }
        catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    public function fetchObjects($class)
    {
        try {
            return $this->statement->fetchAll(PDO::FETCH_CLASS, $class);
        }
        catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    public function fetchArray()
    {
        try {
            return $this->statement->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    public function affectedRows()
    {
        return $this->statement->rowCount();
    }

    public function getRawQuery()
    {
        $keys = array_map(function($key) {
            if (is_string($key)) {
                return '/:' . $key . '/';
            }
            return '/[?]/';
        }, array_keys($this->bindings));
        return preg_replace($keys, $this->bindings, $this->query, 1);
    }

    public function describe($name)
    {
        $columns = $this->query(
            'SHOW COLUMNS FROM ' . $name
        )->fetchArray();
        $dbColumns = array();
        foreach ($columns as $column) {
            $fieldName = $column['Field'];
            array_shift($column);
            $dbColumns[$fieldName] = array_change_key_case($column, CASE_LOWER);
        }
        return $dbColumns;
    }

    public function getBindings()
    {
        return $this->bindings;
    }

    public function getQuery()
    {
        return $this->query;
    }
}
