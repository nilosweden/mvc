<?php

namespace Database;

class QueryBuilder
{
    private $connection = null;
    private $bindings = array();
    private $sqlType = null;
    private $columns = null;
    private $set = null;
    private $from = null;
    private $whereFlag = 0;
    private $join = null;
    private $where = null;
    private $limit = null;
    private $offset = null;
    private $orderBy = null;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function select($columns)
    {
        $this->sqlType = 'SELECT';
        $this->columns = $columns;
        return $this;
    }

    public function update($table)
    {
        $this->sqlType = 'UPDATE';
        $this->from($table);
        return $this;
    }

    public function delete($columns)
    {
        $this->sqlType = 'DELETE';
        $this->columns = $columns;
        return $this;
    }

    public function set($columns)
    {
        $this->set = $columns;
        return $this;
    }

    public function from($table)
    {
        $this->from = $table;
        return $this;
    }

    public function innerJoin($query)
    {
        $this->join[]['inner join'] = $query;
    }

    public function leftJoin($query)
    {
        $this->join[]['left join'] = $query;
    }

    public function rightJoin($query)
    {
        $this->join[]['right join'] = $query;
    }

    public function fullJoin($query)
    {
        $this->join[]['full join'] = $query;
    }

    public function where($query, $binding)
    {
        ++$this->whereFlag;
        $this->where[]['where'] = $query;
        $this->bindings[substr(strstr($query, ':'), 1)] = $binding;
        return $this;
    }

    public function andWhere($query, $binding)
    {
        $this->where[]['and'] = $query;
        $this->bindings[substr(strstr($query, ':'), 1)] = $binding;
        return $this;
    }

    public function orWhere($query, $binding)
    {
        $this->where[]['or'] = $query;
        $this->bindings[substr(strstr($query, ':'), 1)] = $binding;
        return $this;
    }

    public function limit($limit, $offset = null)
    {
        $this->limit = $limit;
        if ($offset) {
            $this->offset = $offset;
        }
        return $this;
    }

    public function orderBy($binding, $descOrAsc)
    {
        $this->orderBy[] = $binding . ' ' . $descOrAsc;
        return $this;
    }

    public function getQuery()
    {
        if (!$this->sqlType) {
            throw new DatabaseException('You need to call either select(), update() or delete()');
        }

        $query = $this->sqlType . ' ';

        if ($this->sqlType == 'SELECT') {
            if (!$this->from) {
                throw new DatabaseException('You need to set from( TABLE ) when using select');
            }
            if (!$this->columns) {
                throw new DatabaseException('You need to set the columns in the select( COLUMNS ) function');
            }
            if ($this->set) {
                throw new DatabaseException('You cannot use the set() function in a select statement');
            }
            $query .= $this->columns . ' FROM ' . $this->from;
        }
        else if($this->sqlType == 'UPDATE') {
            $query .= $this->from;
            $query .= ' SET ' . $this->columns;
        }
        else {
            $query .= 'FROM ' . $this->from;
            if ($this->set) {
                throw new DatabaseException('You cannot use the set() function in a delete statement');
            }
        }

        if ($this->join) {
            foreach ($this->join as $priority => $clause) {
                $key = key($clause);
                $query .= ' ' . mb_strtoupper($key) . ' ' . $clause[$key];
            }
        }

        if ($this->where) {
            if ($this->whereFlag) {
                if ($this->whereFlag > 1) {
                    throw new DatabaseException('You cannot have more than one where() clause');
                }
                $query .= ' WHERE ';
                foreach ($this->where as $priority => $clause) {
                    if (array_key_exists('where', $clause)) {
                        $query .= $clause['where'];
                        unset($this->where[$priority]);
                        break;
                    }
                }
                foreach ($this->where as $priority => $clause) {
                    $key = key($clause);
                    $query .= ' ' . mb_strtoupper(key($clause)) . ' (' . $clause[$key] . ')';
                }
            }
            else {
                $query .= ' WHERE ';
                foreach ($this->where as $priority => $clause) {
                    $key = key($clause);
                    if ($priority == 0) {
                        $query .= '(' . $clause[$key] . ')';
                    }
                    else {
                        $query .= ' ' . mb_strtoupper(key($clause)) . ' (' . $clause[$key] . ')';
                    }
                }
            }
        }

        if ($this->orderBy) {
            $query .= ' ORDER BY ';
            $query .= implode(', ', $this->orderBy);
        }

        if ($this->limit) {
            if (!is_numeric($this->limit)) {
                throw new DatabaseException('The limit( NUMBER ) function accepts only numeric values');
            }
            $query .= ' LIMIT ' . (int)$this->limit;
            if ($this->offset) {
                if (!is_numeric($this->offset)) {
                    throw new DatabaseException('The limit( NUMBER, OFFSET ) function accepts only numeric values');
                }
                $query .= ', ' . (int)$this->offset;
            }
        }
        return $query;
    }

    public function getBindings()
    {
        return $this->bindings;
    }

    public function execute()
    {
        $query = $this->getQuery();
        $bindings = $this->getBindings();
        $queryObj = new Query($this->connection);
        return $queryObj->query($query, $bindings);
    }
}
