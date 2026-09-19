<?php

namespace App\Db\Heart\Mysql\Migration;

use PDO;
use Config;

class _Migration
{
    protected PDO $pdo;

    public function __construct()
    {
        $port = defined('Config\\MYSQL_PORT') ? Config\MYSQL_PORT : 3306;
        $charset = defined('Config\\MYSQL_CHARSET') ? Config\MYSQL_CHARSET : 'utf8mb4';
        $dsn = "mysql:host=" . Config\MYSQL_HOST . ";port={$port};dbname=" . Config\MYSQL_DBNAME . ";charset={$charset}";
        $this->pdo = new PDO($dsn, Config\MYSQL_USERNAME, Config\MYSQL_PASSWORD);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Crea una tabla si no existe, o sincroniza columnas y constraints faltantes
     * sin eliminar ni alterar la información ya existente en la tabla.
     */
    public function create(string $tableName, callable $callback): void
    {
        $structure = new _TableStructure($tableName);
        $callback($structure);

        // Comprobar si la tabla ya existe
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM information_schema.tables 
            WHERE table_schema = DATABASE() AND table_name = ?
        ");
        $stmt->execute([$tableName]);
        $tableExists = (bool)$stmt->fetchColumn();

        if (!$tableExists) {
            $sql = "CREATE TABLE `{$structure->getTable()}` ({$structure->getSql()}) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            $this->pdo->exec($sql);
            return;
        }

        // Si la tabla ya existe, sincronizamos columnas faltantes para preservar los datos existentes
        $colStmt = $this->pdo->prepare("
            SELECT COLUMN_NAME 
            FROM information_schema.columns 
            WHERE table_schema = DATABASE() AND table_name = ?
        ");
        $colStmt->execute([$tableName]);
        $existingColumns = $colStmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

        $definedColumns = $structure->getColumns();
        $columnsToAdd = [];

        foreach ($definedColumns as $colName => $colDef) {
            $colSql = $colDef instanceof _ColumnDefinition ? $colDef->get() : $colDef;

            // Extraer nombre de columna si la clave fue numérica
            if (is_numeric($colName)) {
                if (preg_match('/^`?([a-zA-Z0-9_]+)`?/', trim($colSql), $m)) {
                    $colName = $m[1];
                }
            }

            if (!in_array($colName, $existingColumns)) {
                $columnsToAdd[] = "ADD COLUMN $colSql";
            }
        }

        if (!empty($columnsToAdd)) {
            $alterSql = "ALTER TABLE `$tableName` " . implode(', ', $columnsToAdd);
            $this->pdo->exec($alterSql);
            echo "   ↳ 🔄 Tabla '$tableName' actualizada: " . count($columnsToAdd) . " columna(s) agregada(s) conservando los datos existentes.\n";
        }

        // Sincronizar índices / constraints faltantes
        $idxStmt = $this->pdo->prepare("
            SELECT INDEX_NAME 
            FROM information_schema.statistics 
            WHERE table_schema = DATABASE() AND table_name = ?
        ");
        $idxStmt->execute([$tableName]);
        $existingIndexes = $idxStmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

        foreach ($structure->getConstraints() as $constraint) {
            $constraintName = null;
            if (preg_match('/(?:INDEX|UNIQUE|CONSTRAINT)\s+`?([a-zA-Z0-9_]+)`?/i', $constraint, $m)) {
                $constraintName = $m[1];
            }

            if (!$constraintName || !in_array($constraintName, $existingIndexes)) {
                try {
                    $this->pdo->exec("ALTER TABLE `$tableName` ADD $constraint");
                } catch (\PDOException $e) {
                    echo "   ↳ ⚠️ Advertencia al agregar constraint '$constraintName': " . $e->getMessage() . "\n";
                }
            }
        }
    }

    /**
     * Elimina una tabla si existe
     */
    public function drop(string $tableName): void
    {
        $this->pdo->exec("DROP TABLE IF EXISTS `$tableName`");
    }

    /**
     * Modifica una tabla ejecutando la estructura definida en el callback,
     * agregando únicamente las columnas y constraints que aún no existan.
     */
    public function alter(string $tableName, callable $callback): void
    {
        $structure = new _TableStructure($tableName);
        $structure->markAsAlter();
        $callback($structure);

        $colStmt = $this->pdo->prepare("
            SELECT COLUMN_NAME 
            FROM information_schema.columns 
            WHERE table_schema = DATABASE() AND table_name = ?
        ");
        $colStmt->execute([$tableName]);
        $existingColumns = $colStmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

        $actions = [];

        foreach ($structure->getColumns() as $colName => $colDef) {
            $colSql = $colDef instanceof _ColumnDefinition ? $colDef->get() : $colDef;

            if (is_numeric($colName)) {
                if (preg_match('/^`?([a-zA-Z0-9_]+)`?/', trim($colSql), $m)) {
                    $colName = $m[1];
                }
            }

            if (!in_array($colName, $existingColumns)) {
                $actions[] = "ADD COLUMN $colSql";
            }
        }

        $idxStmt = $this->pdo->prepare("
            SELECT INDEX_NAME 
            FROM information_schema.statistics 
            WHERE table_schema = DATABASE() AND table_name = ?
        ");
        $idxStmt->execute([$tableName]);
        $existingIndexes = $idxStmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

        foreach ($structure->getConstraints() as $constraint) {
            $constraintName = null;
            if (preg_match('/(?:INDEX|UNIQUE|CONSTRAINT)\s+`?([a-zA-Z0-9_]+)`?/i', $constraint, $m)) {
                $constraintName = $m[1];
            }

            if (!$constraintName || !in_array($constraintName, $existingIndexes)) {
                $actions[] = "ADD $constraint";
            }
        }

        if (!empty($actions)) {
            $sql = "ALTER TABLE `$tableName` " . implode(', ', $actions);
            $this->pdo->exec($sql);
        }
    }

    /**
     * Elimina una columna de una tabla de forma segura (solo si existe)
     */
    public function dropColumn(string $table, string $column): void
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM information_schema.columns 
            WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?
        ");
        $stmt->execute([$table, $column]);
        if ($stmt->fetchColumn() > 0) {
            $this->pdo->exec("ALTER TABLE `$table` DROP COLUMN `$column`");
        }
    }

    /**
     * Registra una migración como ejecutada
     */
    public function recordMigration(string $migrationFile): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO cj_migrations (migration, created_at, updated_at) VALUES (?, NOW(), NOW())");
        $stmt->execute([$migrationFile]);
    }

    /**
     * Devuelve las migraciones ya ejecutadas
     */
    public function getExecutedMigrations(): array
    {
        $stmt = $this->pdo->query("SELECT migration FROM cj_migrations");
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    public function raw(string $sql): void
    {
        $this->pdo->exec($sql);
    }
}
