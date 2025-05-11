<?php

namespace App\Db\Heart\Mysql\Migration;

use PDO;
use Config;

class _Migration
{
    protected PDO $pdo;

    public function __construct()
    {
        $dsn = 'mysql:host=' . Config\MYSQL_HOST . ';dbname=' . Config\MYSQL_DBNAME . ';charset=' . Config\MYSQL_CHARSET;
        $this->pdo = new PDO($dsn, Config\MYSQL_USERNAME, Config\MYSQL_PASSWORD);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Crea una tabla ejecutando la estructura definida en el callback
     */
    public function create(string $tableName, callable $callback): void
    {
        $structure = new _TableStructure($tableName);
        $callback($structure);

        $sql = "CREATE TABLE IF NOT EXISTS `{$structure->getTable()}` ({$structure->getSql()})";
        $this->pdo->exec($sql);
    }

    /**
     * Elimina una tabla si existe
     */
    public function drop(string $tableName): void
    {
        $this->pdo->exec("DROP TABLE IF EXISTS `$tableName`");
    }

    /**
     * Modifica una tabla ejecutando la estructura definida en el callback
     */
    public function alter(string $tableName, callable $callback): void
    {
        $structure = new _TableStructure($tableName);
        $structure->markAsAlter(); // nuevo método
        $callback($structure);

        $sql = "ALTER TABLE `$tableName` " . $structure->getSql();
        $this->pdo->exec($sql);
    }

    /**
     * Elimina una columna de una tabla
     */
    public function dropColumn(string $table, string $column): void
    {
        $this->pdo->exec("ALTER TABLE `$table` DROP COLUMN `$column`");
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
