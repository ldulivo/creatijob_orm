<?php
namespace App\Db;

use PDOException;
use App\Core\Logger;
use App\Db\Database;
use App\Db\DatabaseUtils;
use App\Db\Heart\Where;

/**
 * Class QueryBuilder
 * -----
 * Description:
 * This class provides an easy and flexible way to build SQL queries dynamically.
 * It allows for the construction of SELECT, INSERT, UPDATE, and DELETE queries with methods for filtering, ordering, limiting results, and handling parameters.
 * 
 * Details:
 * 1. The `table` method defines the table for the query.
 * 2. The `select` method defines which columns to select.
 * 3. The `where` method adds a WHERE clause to the query.
 * 4. The `orderBy` method allows you to define sorting order.
 * 5. The `limit` and `offset` methods define pagination parameters.
 * 6. The `get` method retrieves the results from a SELECT query.
 * 7. The `count` method retrieves the count of records that match the WHERE condition.
 * 8. The `insert` method performs an INSERT operation.
 * 9. The `update` method performs an UPDATE operation.
 * 10. The `delete` method performs a DELETE operation.
 * 11. Errors are logged into the error log using the Logger class.
 * 
 * -----
 */
class QueryBuilder extends Where {
    private $table;
    private $columns = '*';
    private $orderBy = '';
    private $limit = '';
    private $offset = '';
    private $set = '';
    private static $logger;

    public function __construct()
    {
        self::$logger = new Logger(API_PATH . '/log/error.log'); // Initialize the logger
    }

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
    public function table($table) {
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
    public function select($columns = '*') {
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
    public function orderBy($column, $direction = 'ASC') {
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
    public function limit($limit) {
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
    public function offset($offset) {
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
            return Database::fetchAll($sql, $this->params);
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
    public function count() {
        try {
            $sql = "SELECT COUNT(*) FROM $this->table " . $this->buildWhere();
            $result = Database::fetch($sql, $this->params);
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
    public function insert($data) {
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $sql = "INSERT INTO $this->table ($columns) VALUES ($placeholders)";
        try {
            return Database::execute($sql, array_values($data));
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
    public function update($data) {
        $whereClause = $this->buildWhere();
        $recordExists = DatabaseUtils::recordExists($this->table, $whereClause, $this->params);

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
            $affectedRows = Database::execute($sql, array_merge(array_values($data), $this->params));
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
    public function delete() {
        $whereClause = $this->buildWhere();
        $recordExists = DatabaseUtils::recordExists($this->table, $whereClause, $this->params);

        if (!$recordExists) {
            return false;
        }

        $sql = "DELETE FROM $this->table $whereClause";
        try {
            return Database::execute($sql, $this->params);
        } catch (PDOException $e) {
            $msgError = $e->getMessage();
            if ($e->errorInfo) {
                $msgError = "Delete query error: " . $e->getMessage();
            }
            throw new \PDOException($msgError);
        }
    }
}
?>
