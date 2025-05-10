<?php

namespace App\Db;

use Config;

abstract class Migration
{
    protected $table;
    private $migration;

    public function __construct() {
        switch (Config\DB_DRIVER) {
            case 'mysql':
                $mainMigration = \App\Db\Heart\Mysql\Migration\_Migration::class;
                break;

            default:
                throw new \Exception("Driver no soportado");
        }
        $this->migration = new $mainMigration();
    }

    abstract public function up();
    abstract public function down();

    protected function create(string $tableName, callable $callback): void {
        $this->migration->create(strtolower($tableName), $callback);
    }
    protected function drop(string $tableName): void {
        $this->migration->drop($tableName);
    }

    protected function recordMigration(string $migrationFile): void {
        $this->migration->recordMigration($migrationFile);
    }

    protected function getExecutedMigrations(): array {
        return $this->migration->getExecutedMigrations();
    }

    public function raw(string $sql): void {
        $this->migration->raw($sql);
    }
}
