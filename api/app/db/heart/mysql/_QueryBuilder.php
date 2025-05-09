<?php

namespace App\Db\Heart\Mysql;

use PDOException;

class _QueryBuilder extends _Where
{
    protected $table;
    protected $columns = '*';
    protected $orderBy = '';
    protected $limit = '';
    protected $offset = '';
    protected $set = '';

    /**
     * Method: table
     * -----
     * Description:
     * Defines the table to perform the query on.
     * 
     * @param string $table The table name.
     * @return $this The current instance of the QueryBuilder.
     * -----
     */
    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Method: select
     * -----
     * Description:
     * Defines which columns to select in the query. Defaults to `*` (all columns).
     * 
     * @param string $columns The columns to select (comma-separated).
     * @return $this The current instance of the QueryBuilder.
     * -----
     */
    public function select($columns = '*')
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * Method: where
     * -----
     * Description:
     * Adds a WHERE condition to the query to filter the results.
     * 
     * @param string $column The column name to filter by.
     * @param string $operator The operator for the condition (e.g., '=', '>', '<').
     * @param mixed $value The value to compare the column to.
     * @return $this The current instance of the QueryBuilder.
     * -----
     */
    public function where($column, $operatorKey, $value)
    {
        $this->addWhere($column, $operatorKey, $value);
        return $this;
    }

    public function orWhere($column, $operatorKey, $value)
    {
        $this->addWhere($column, $operatorKey, $value, 'OR');
        return $this;
    }

    /**
     * Method: orderBy
     * -----
     * Description:
     * Adds an ORDER BY clause to the query to define sorting of the results.
     * 
     * @param string $column The column to order by.
     * @param string $direction The direction to sort (`ASC` or `DESC`).
     * @return $this The current instance of the QueryBuilder.
     * -----
     */
    public function orderBy($column, $direction = 'ASC')
    {
        $this->orderBy = "ORDER BY $column $direction";
        return $this;
    }

    /**
     * Method: limit
     * -----
     * Description:
     * Adds a LIMIT clause to the query to restrict the number of results.
     * 
     * @param int $limit The maximum number of results to return.
     * @return $this The current instance of the QueryBuilder.
     * -----
     */
    public function limit($limit)
    {
        $this->limit = "LIMIT $limit";
        return $this;
    }

    /**
     * Method: offset
     * -----
     * Description:
     * Adds an OFFSET clause to the query for pagination.
     * 
     * @param int $offset The number of records to skip before starting to return results.
     * @return $this The current instance of the QueryBuilder.
     * -----
     */
    public function offset($offset)
    {
        $this->offset = "OFFSET $offset";
        return $this;
    }

    /**
     * Method: get
     * -----
     * Description:
     * Executes a SELECT query and returns the results as an array of associative arrays.
     * It logs any errors encountered during the execution of the query.
     * 
     * @return array The results of the query.
     * -----
     */
    public function get()
    {
        $sql = "SELECT $this->columns FROM $this->table " . $this->buildWhere() . " $this->orderBy $this->limit $this->offset";
        try {
            return self::fetchAll($sql, $this->params);
        } catch (PDOException $e) {
            $msgError = $e->getMessage();
            if ($e->errorInfo) {
                $msgError = "Get query error: " . $e->getMessage();
            }
            throw new \PDOException($msgError);
        }
    }

    /**
     * Method: count
     * -----
     * Description:
     * Executes a SELECT COUNT(*) query to return the number of records that match the WHERE condition.
     * It logs any errors encountered during the execution of the query.
     * 
     * @return int The number of matching records.
     * -----
     */
    public function count()
    {
        try {
            $sql = "SELECT COUNT(*) FROM $this->table " . $this->buildWhere();
            $result = self::fetch($sql, $this->params);
            return $result['COUNT(*)'];
        } catch (PDOException $e) {
            $msgError = $e->getMessage();
            if ($e->errorInfo) {
                $msgError = "Count query error: " . $e->getMessage();
            }
            throw new \PDOException($msgError);
        }
    }

    /**
     * Method: insert
     * -----
     * Description:
     * Executes an INSERT query to insert new data into the table.
     * It logs any errors encountered during the execution of the query.
     * 
     * @param array $data The data to insert (associative array of column => value).
     * @return string The last inserted ID from the database.
     * -----
     */
    public function insert($data)
    {
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $sql = "INSERT INTO $this->table ($columns) VALUES ($placeholders)";
        try {
            self::execute($sql, array_values($data));
            return self::getConnection()->lastInsertId();
        } catch (PDOException $e) {
            $msgError = $e->getMessage();
            if ($e->errorInfo) {
                $msgError = "Insert query error: " . $e->getMessage();
            }
            throw new \PDOException($msgError);
        }
    }

    /**
     * Method: update
     * -----
     * Description:
     * Executes an UPDATE query to modify existing data in the table.
     * It logs any errors encountered during the execution of the query.
     * 
     * @param array $data The data to update (associative array of column => new value).
     * @return int The number of rows affected by the update.
     * -----
     */
    public function update($data)
    {
        $whereClause = $this->buildWhere();
        $recordExists = _Utils::recordExists($this->table, $whereClause, $this->params);

        if (!$recordExists) {
            return false;
        }

        $set = '';
        foreach ($data as $column => $value) {
            $set .= "$column = ?, ";
        }
        $set = rtrim($set, ', ');

        $sql = "UPDATE $this->table SET $set $whereClause";
        try {
            $affectedRows = self::execute($sql, array_merge(array_values($data), $this->params));
            return $affectedRows === 0 ? null : $affectedRows;
        } catch (PDOException $e) {
            $msgError = $e->getMessage();
            if ($e->errorInfo) {
                $msgError = "Update query error: " . $e->getMessage();
            }
            throw new \PDOException($msgError);
        }
    }

    /**
     * Method: delete
     * -----
     * Description:
     * Executes a DELETE query to remove data from the table.
     * It logs any errors encountered during the execution of the query.
     * 
     * @return int The number of rows affected by the deletion.
     * -----
     */
    public function delete()
    {
        $whereClause = $this->buildWhere();
        $recordExists = _Utils::recordExists($this->table, $whereClause, $this->params);

        if (!$recordExists) {
            return false;
        }

        $sql = "DELETE FROM $this->table $whereClause";
        try {
            return self::execute($sql, $this->params);
        } catch (PDOException $e) {
            $msgError = $e->getMessage();
            if ($e->errorInfo) {
                $msgError = "Delete query error: " . $e->getMessage();
            }
            throw new \PDOException($msgError);
        }
    }
}
