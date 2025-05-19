<?php

namespace App\Db\Heart\Mysql\Migration;

class _TableStructure
{
    private string $tableName;
    private array $columns = [];
    private array $constraints = [];
    private bool $alterMode = false;

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    public function id(string $name = 'id'): void
    {
        $this->columns[] = "`$name` INT AUTO_INCREMENT PRIMARY KEY";
    }

    public function string(string $name, int $length = 255): _ColumnDefinition
    {
        $col = new _ColumnDefinition($name, "VARCHAR($length)");
        $this->columns[$name] = $col;
        return $col;
    }

    public function text(string $name): _ColumnDefinition
    {
        $col = new _ColumnDefinition($name, "TEXT");
        $this->columns[$name] = $col;
        return $col;
    }

    public function integer(string $name): _ColumnDefinition
    {
        $col = new _ColumnDefinition($name, "INT");
        $this->columns[$name] = $col;
        return $col;
    }

    public function boolean(string $name): _ColumnDefinition
    {
        $col = new _ColumnDefinition($name, "TINYINT(1)");
        $this->columns[$name] = $col;
        return $col;
    }

    public function float(string $name, int $total = 8, int $decimals = 2): _ColumnDefinition
    {
        $col = new _ColumnDefinition($name, "FLOAT($total, $decimals)");
        $this->columns[$name] = $col;
        return $col;
    }

    public function enum(string $name, array $values): void
    {
        $escaped = array_map(fn($v) => "'$v'", $values);
        $this->columns[] = "`$name` ENUM(" . implode(', ', $escaped) . ")";
    }

    public function date(string $name): void
    {
        $this->columns[] = "`$name` DATE";
    }

    public function datetime(string $name): void
    {
        $this->columns[] = "`$name` DATETIME";
    }

    public function timestamps(): void
    {
        $this->columns[] = "`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "`updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
    }

    public function nullable(string $name, string $type = 'VARCHAR', int $length = 255): void
    {
        $definition = match (strtoupper($type)) {
            'VARCHAR' => "`$name` VARCHAR($length) NULL",
            'TEXT'    => "`$name` TEXT NULL",
            'INT'     => "`$name` INT NULL",
            'FLOAT'   => "`$name` FLOAT($length) NULL",
            default   => "`$name` $type NULL",
        };
        $this->columns[] = $definition;
    }

    public function index(string $columnName, ?string $indexName = null): void
    {
        $index = $indexName ?? "{$columnName}_idx";
        $this->constraints[] = "INDEX `$index` (`$columnName`)";
    }

    public function unique(string|array $columns, ?string $name = null): void
    {
        if (is_array($columns)) {
            $name = $name ?? 'unique_' . implode('_', $columns);
            $cols = implode('`, `', $columns);
            $this->constraints[] = "UNIQUE `$name` (`$cols`)";
        } else {
            $name = $name ?? "{$columns}_unique";
            $this->constraints[] = "UNIQUE `$name` (`$columns`)";
        }
    }

    public function foreign(
        string $columnName,
        string $foreignTable,
        string $foreignColumn = 'id',
        string $onDelete = 'CASCADE',
        string $onUpdate = 'CASCADE',
        ?string $fkName = null
    ): void {
        $fk = $fkName ?? "fk_{$this->tableName}_{$columnName}";
        $this->constraints[] =
            "CONSTRAINT `$fk` FOREIGN KEY (`$columnName`) REFERENCES `$foreignTable`(`$foreignColumn`) ON DELETE $onDelete ON UPDATE $onUpdate";
    }

    public function getSql(): string
    {
        $cols = array_map(fn($col) => $col instanceof _ColumnDefinition ? $col->get() : $col, $this->columns);
        return $this->alterMode
            ? implode(', ', array_map(fn($c) => "ADD COLUMN $c", $cols))
            : implode(', ', array_merge($cols, $this->constraints));
    }

    public function getTable(): string
    {
        return $this->tableName;
    }

    public function primary(array $columns, ?string $name = null): void
    {
        $name = $name ?? 'PRIMARY';
        $cols = implode('`, `', $columns);
        $this->constraints[] = "CONSTRAINT `$name` PRIMARY KEY (`$cols`)";
    }

    public function uniqueComposite(array $columns, ?string $name = null): void
    {
        $name = $name ?? 'unique_' . implode('_', $columns);
        $cols = implode('`, `', $columns);
        $this->constraints[] = "UNIQUE `$name` (`$cols`)";
    }

    public function indexComposite(array $columns, ?string $name = null): void
    {
        $name = $name ?? 'index_' . implode('_', $columns);
        $cols = implode('`, `', $columns);
        $this->constraints[] = "INDEX `$name` (`$cols`)";
    }

    public function markAsAlter(): void
    {
        $this->alterMode = true;
    }
}
