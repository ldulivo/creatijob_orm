<?php

namespace App\Db\Heart\Sqlsrv;

use PDOException;

class _Utils extends _Database
{

    // Sanitizar entradas de texto
    public static function sanitize($data)
    {
        try {
            return htmlspecialchars(strip_tags($data));
        } catch (PDOException $e) {
            $msgError = $e->getMessage();
            if ($e->errorInfo) {
                $msgError = "Sanitize error: " . $e->getMessage();
            }
            throw new \PDOException($msgError);
        }
    }

    // Verificar la conexión con la base de datos
    public static function checkConnection()
    {
        try {
            self::getConnection();
            return true;
        } catch (PDOException $e) {
            $msgError = $e->getMessage();
            if ($e->errorInfo) {
                $msgError = "Check connection error: " . $e->getMessage();
            }
            throw new \PDOException($msgError);
            return false;
        }
    }

    // Iniciar una transacción
    public static function beginTransaction()
    {
        try {
            $pdo = self::getConnection();
            $pdo->beginTransaction();
        } catch (PDOException $e) {
            $msgError = $e->getMessage();
            if ($e->errorInfo) {
                $msgError = "Begin transaction error: " . $e->getMessage();
            }
            throw new \PDOException($msgError);
        }
    }

    // Confirmar una transacción
    public static function commit()
    {
        try {
            $pdo = self::getConnection();
            $pdo->commit();
        } catch (PDOException $e) {
            $msgError = $e->getMessage();
            if ($e->errorInfo) {
                $msgError = "Commit error: " . $e->getMessage();
            }
            throw new \PDOException($msgError);
        }
    }

    // Revertir una transacción
    public static function rollback()
    {
        try {
            $pdo = self::getConnection();
            $pdo->rollBack();
        } catch (PDOException $e) {
            $msgError = $e->getMessage();
            if ($e->errorInfo) {
                $msgError = "Rollback error: " . $e->getMessage();
            }
            throw new \PDOException($msgError);
        }
    }

    /**
     * Method: exists
     * -----
     * Description:
     * Checks if a record exists in the specified table and column.
     * Uses COUNT(*) and returns true if at least one record is found.
     * 
     * @param string $table The table name.
     * @param string $column The column name to search.
     * @param mixed $value The value to match.
     * @return bool True if the record exists, false otherwise.
     * -----
     */
    public static function exists($table, $column, $value)
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM $table WHERE $column = ?";
            $result = self::fetch($sql, [$value]);
            return $result['total'] > 0;
        } catch (PDOException $e) {
            $msgError = $e->getMessage();
            if ($e->errorInfo) {
                $msgError = "Exists check error: " . $e->getMessage();
            }
            throw new \PDOException($msgError);
        }
    }

    // Escapar valores para evitar inyecciones SQL
    public static function escape($value)
    {
        try {
            return self::getConnection()->quote($value);
        } catch (PDOException $e) {
            $msgError = $e->getMessage();
            if ($e->errorInfo) {
                $msgError = "Escape error: " . $e->getMessage();
            }
            throw new \PDOException($msgError);
        }
    }

    // Convertir un array a formato adecuado para un INSERT (evitar SQL injection)
    public static function prepareInsertData($data)
    {
        try {
            $keys = array_keys($data);
            $columns = implode(", ", $keys);
            $placeholders = implode(", ", array_fill(0, count($data), "?"));
            return [
                'columns' => $columns,
                'placeholders' => $placeholders,
                'values' => array_values($data),
            ];
        } catch (PDOException $e) {
            $msgError = $e->getMessage();
            if ($e->errorInfo) {
                $msgError = "Prepare insert data error: " . $e->getMessage();
            }
            throw new \PDOException($msgError);
        }
    }

    /**
     * Method: recordExists
     * -----
     * Description:
     * Checks if any record exists in a table for the given WHERE clause and parameters.
     * 
     * @param string $table The table name.
     * @param string $whereClause The WHERE clause (e.g., "WHERE id = ?").
     * @param array $params The parameters to bind in the WHERE clause.
     * @return bool True if record exists, false otherwise.
     * -----
     */
    public static function recordExists($table, $whereClause, $params)
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM $table $whereClause";
            $result = self::fetch($sql, $params);
            return $result['total'] > 0;
        } catch (PDOException $e) {
            $msgError = $e->getMessage();
            if ($e->errorInfo) {
                $msgError = "Error checking record existence: " . $e->getMessage();
            }
            throw new \PDOException($msgError);
        }
    }
}
