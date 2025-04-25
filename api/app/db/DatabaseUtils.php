<?php
namespace App\Db;

use PDOException;
use App\Db\Database;

class DatabaseUtils {

    // Sanitizar entradas de texto
    public static function sanitize($data) {
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
    public static function checkConnection() {
        try {
            Database::getConnection();
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
    public static function beginTransaction() {
        try {
            $pdo = Database::getConnection();
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
    public static function commit() {
        try {
            $pdo = Database::getConnection();
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
    public static function rollback() {
        try {
            $pdo = Database::getConnection();
            $pdo->rollBack();
        } catch (PDOException $e) {
            $msgError = $e->getMessage();
            if ($e->errorInfo) {
                $msgError = "Rollback error: " . $e->getMessage();
            }
            throw new \PDOException($msgError);
        }
    }

    // Verificar si un registro existe (por ejemplo, para evitar duplicados)
    public static function exists($table, $column, $value) {
        try {
            $sql = "SELECT COUNT(*) FROM $table WHERE $column = ?";
            $result = Database::fetch($sql, [$value]);
            return $result['COUNT(*)'] > 0;
        } catch (PDOException $e) {
            $msgError = $e->getMessage();
            if ($e->errorInfo) {
                $msgError = "Exists check error: " . $e->getMessage();
            }
            throw new \PDOException($msgError);
        }
    }

    // Escapar valores para evitar inyecciones SQL
    public static function escape($value) {
        try {
            return Database::getConnection()->quote($value);
        } catch (PDOException $e) {
            $msgError = $e->getMessage();
            if ($e->errorInfo) {
                $msgError = "Escape error: " . $e->getMessage();
            }
            throw new \PDOException($msgError);
        }
    }

    // Convertir un array a formato adecuado para un INSERT (evitar SQL injection)
    public static function prepareInsertData($data) {
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

    public static function recordExists($table, $whereClause, $params) {
        try {
            // Construir la consulta SELECT COUNT(*)
            $sql = "SELECT COUNT(*) FROM $table $whereClause";
            $result = Database::fetch($sql, $params);
            
            // Si el conteo es mayor que 0, el registro existe
            return $result['COUNT(*)'] > 0;
        } catch (PDOException $e) {
            $msgError = $e->getMessage();
            if ($e->errorInfo) {
                $msgError = "Error checking record existence: " . $e->getMessage();
            }
            throw new \PDOException($msgError);
        }
    }
}

?>
