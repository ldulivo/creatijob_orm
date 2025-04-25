<?php
namespace App\Core;

class Context
{
    private static array $data = [];

    public static function set(string $key, $value): void
    {
        self::$data[$key] = $value;
    }

    public static function get(string $key)
    {
        return self::$data[$key] ?? null;
    }

    public static function has(string $key): bool
    {
        return isset(self::$data[$key]);
    }

    public static function all(): array
    {
        return self::$data;
    }

    public static function clear(): void
    {
        self::$data = [];
    }
}
