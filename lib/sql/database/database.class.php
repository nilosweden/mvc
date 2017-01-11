<?php

namespace Database;

class Database
{
    private static $connectionHandler;
    private static $currentConnection;
    private static $defaultConnectionSection = 'default';
    private static $defaultConnectionName = 'default';

    public static function newConnectionHandler(array $config)
    {
        static::$connectionHandler = new ConnectionHandler($config);
        static::$currentConnection = static::$connectionHandler->getConnection(
            static::$defaultConnectionName,
            static::$defaultConnectionSection
        );
    }

    public static function newConnection(array $config)
    {
        return static::$connectionHandler->newConnection($config);
    }

    public static function setDefaultConnection($section, $name)
    {
        static::$currentConnection = static::$connectionHandler->getConnection($section, $name);
        return static::$currentConnection;
    }

    public static function getPDO()
    {
        return static::$currentConnection->getPDO();
    }

    public static function getConnection($section, $name)
    {
        return static::$connectionHandler->getConnection($section, $name);
    }

    public static function getConnections()
    {
        return static::$connectionHandler->getConnections();
    }

    public static function useConnection($section, $name)
    {
        return new Query(static::$connectionHandler->getConnection($section, $name));
    }

    public static function query($query, array $bindings = array())
    {
        $queryObj = new Query(static::$currentConnection);
        return $queryObj->query($query, $bindings);
    }

    public static function buildQuery()
    {
        return new QueryBuilder(static::$currentConnection);
    }

    public static function transaction()
    {
        $queryObj = new Query(static::$currentConnection);
        return $queryObj->transaction();
    }
}
