<?php
namespace App\Db;

use PDO;
use PDOException;
use Config;

/**
 * Class Database
 * -----
 * Description:
 * This class manages the connection to the database and provides methods to execute queries,
 * retrieve data, and perform CRUD operations (Insert, Update, Delete).
 * It uses PDO for interacting with the MySQL database and logs any database-related errors.
 * 
 * Details:
 * 1. The `getConnection` method initializes and returns a PDO connection to the database.
 * 2. The `query` method prepares and executes a SQL query with optional parameters.
 * 3. The `fetchAll` method retrieves all results from a SELECT query.
 * 4. The `fetch` method retrieves a single result from a SELECT query.
 * 5. The `insert` method inserts data into the database and returns the last inserted ID.
 * 6. The `execute` method is used for UPDATE, DELETE, or other queries that do not return data.
 * 7. Errors are logged into the error log using the Logger class.
 * 
 * -----
 */
class Database {
    private static $pdo;

    /**
     * Method: getConnection
     * -----
     * Description:
     * Establishes and returns a PDO connection to the MySQL database.
     * It logs any connection errors to the error log.
     * 
     * @return PDO The PDO instance for the database connection.
     * -----
     */
    public static function getConnection() {
        try {
            if (!self::$pdo) {
                $dsn = 'mysql:host=' . Config\DB_HOST . ';dbname=' . Config\DB_NAME . ';charset=' . Config\DB_CHARSET;
                self::$pdo = new PDO($dsn, Config\DB_USERNAME, Config\DB_PASSWORD);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            return self::$pdo;
        } catch (PDOException $e) {
            $msgError = $e->getMessage();
            if ($e->errorInfo) {
                $msgError = "Database connection failed: " . $e->getMessage();
            }
            throw new \PDOException($msgError);
        }
    }

    /**
     * Method: query
     * -----
     * Description:
     * Prepares and executes an SQL query with optional parameters.
     * It logs any errors encountered during query execution.
     * 
     * @param string $sql The SQL query to execute.
     * @param array $params The parameters to bind to the query (default is an empty array).
     * @return PDOStatement The prepared statement after execution.
     * -----
     */
    public static function query($sql, $params = []) {
        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            $msgError = $e->getMessage();
            if ($e->errorInfo) {
                $msgError = "Query execution failed: " . $e->getMessage();
            }
            throw new \PDOException($msgError);
        }
    }

    /**
     * Method: fetchAll
     * -----
     * Description:
     * Executes a SELECT query and retrieves all results.
     * It logs any errors encountered during the execution of the query.
     * 
     * @param string $sql The SQL query to execute.
     * @param array $params The parameters to bind to the query (default is an empty array).
     * @return array An array of results from the SELECT query.
     * -----
     */
    public static function fetchAll($sql, $params = []) {
        try {
            $stmt = self::query($sql, $params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $msgError = $e->getMessage();
            if ($e->errorInfo) {
                $msgError = "FetchAll failed: " . $e->getMessage();
            }
            throw new \PDOException($msgError);
        }
    }

    /**
     * Method: fetch
     * -----
     * Description:
     * Executes a SELECT query and retrieves a single result.
     * It logs any errors encountered during the execution of the query.
     * 
     * @param string $sql The SQL query to execute.
     * @param array $params The parameters to bind to the query (default is an empty array).
     * @return array|null The first result from the SELECT query, or null if no result found.
     * -----
     */
    public static function fetch($sql, $params = []) {
        try {
            $stmt = self::query($sql, $params);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $msgError = $e->getMessage();
            if ($e->errorInfo) {
                $msgError = "Fetch failed: " . $e->getMessage();
            }
            throw new \PDOException($msgError);
        }
    }

    /**
     * Method: insert
     * -----
     * Description:
     * Executes an INSERT query and returns the last inserted ID.
     * It logs any errors encountered during the execution of the query.
     * 
     * @param string $sql The SQL query to execute.
     * @param array $params The parameters to bind to the query (default is an empty array).
     * @return string The last inserted ID from the database.
     * -----
     */
    public static function insert($sql, $params = []) {
        try {
            self::query($sql, $params);
            return self::getConnection()->lastInsertId();
        } catch (PDOException $e) {
            $msgError = $e->getMessage();
            if ($e->errorInfo) {
                $msgError = "Insert failed: " . $e->getMessage();
            }
            throw new \PDOException($msgError);
        }
    }

    /**
     * Method: execute
     * -----
     * Description:
     * Executes a query for UPDATE, DELETE, or other operations that do not return data.
     * It logs any errors encountered during the execution of the query.
     * 
     * @param string $sql The SQL query to execute.
     * @param array $params The parameters to bind to the query (default is an empty array).
     * @return int The number of affected rows.
     * -----
     */
    public static function execute($sql, $params = []) {
        try {
            $stmt = self::query($sql, $params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            $msgError = $e->getMessage();
            if ($e->errorInfo) {
                $msgError = "Execute failed: " . $e->getMessage();
            }
            throw new \PDOException($msgError);
        }
    }
}
?>
