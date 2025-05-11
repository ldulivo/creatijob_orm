<?php

define('IS_CLI', php_sapi_name() === 'cli');
if (!IS_CLI) {
    // Este script solo se puede ejecutar desde la línea de comandos.
    exit;
}

define('ROOT_PATH', dirname(__DIR__));
if (!defined('ROOT_PATH')) {
    die("No se puede ejecutar este script directamente.");
}
define('API_PATH', ROOT_PATH);
define('WEB_ROOT', dirname(ROOT_PATH));
define('_CMD', $argv[1] ?? null);
define('_ARG', $argv[2] ?? null);
define('_ARGC', $argc);

require_once(API_PATH . '/Config.php');
require_once(API_PATH . '/app/core/Autoloader.php');


if (!_CMD) {
    echo "Uso: ./creatijob [make:migration|migrate|rollback] [Nombre]\n";
    exit;
}

switch (_CMD) {
    case 'make:migration':
        if (!_ARG) {
            echo "Debes especificar el nombre de la migración.\n";
            exit;
        }
        require ROOT_PATH . '/commands/generate.php';
        generate_migration();
        break;

    case 'migrate':
        $_SERVER['CJ_ACTION'] = 'migrate';
        require ROOT_PATH . '/commands/migrate.php';
        break;

    case 'rollback':
        $_SERVER['CJ_ACTION'] = 'rollback';
        require ROOT_PATH . '/commands/migrate.php';
        break;

    case 'make:resource':
        require ROOT_PATH . '/commands/resource.php';
        generate_resource();
        break;

    case 'help':
        require ROOT_PATH . '/commands/help.php';
        break;

    default:
        echo "Unknown command: " . _CMD . "\n";
        echo "Available commands: make:migration, migrate, rollback, make:resource, help\n";
        echo "Use 'help' for more information.\n";
        exit(1);
}
