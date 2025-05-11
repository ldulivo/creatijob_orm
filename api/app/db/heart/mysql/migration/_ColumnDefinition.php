<?php

namespace App\Db\Heart\Mysql\Migration;

class _ColumnDefinition
{
    public string $definition;
    public string $name;
    public array $modifiers = [];
    private ?string $default = null;
    private ?string $comment = null;
    private bool $isPrimary = false;

    public function __construct(string $name, string $base)
    {
        $this->name = $name;
        $this->definition = "`$name` $base NOT NULL";
    }

    public function unique(): self
    {
        $this->modifiers[] = "UNIQUE";
        return $this;
    }

    public function nullable(): self
    {
        $this->definition = str_replace("NOT NULL", "", $this->definition);
        $this->modifiers[] = "NULL";
        return $this;
    }

    public function default($value): self
    {
        $this->default = is_string($value)
            ? "'" . addslashes($value) . "'"
            : (is_null($value) ? "NULL" : $value);
        return $this;
    }

    public function comment(string $text): self
    {
        $this->comment = "'" . addslashes($text) . "'";
        return $this;
    }

    public function primary(): self
    {
        $this->isPrimary = true;
        return $this;
    }

    public function get(): string
    {
        $parts = [$this->definition];

        if ($this->default !== null) {
            $parts[] = "DEFAULT {$this->default}";
        }

        if ($this->comment !== null) {
            $parts[] = "COMMENT {$this->comment}";
        }

        if ($this->isPrimary) {
            $parts[] = "PRIMARY KEY";
        }

        $parts = array_merge($parts, $this->modifiers);

        return trim(implode(' ', $parts));
    }
}
