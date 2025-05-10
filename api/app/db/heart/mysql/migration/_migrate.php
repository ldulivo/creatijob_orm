<?php

namespace App\Db\Heart\Mysql\Migration;

use App\Db\Heart\Mysql\Migration\_Migration;
use PDO;
use Config;

$migrationsPath = ROOT_PATH . '/migrations/';
$files = glob($migrationsPath . '*.php');
$action = $_SERVER['CJ_ACTION'] ?? 'migrate';

$pdo = new PDO(
    'mysql:host=' . Config\MYSQL_HOST . ';dbname=' . Config\MYSQL_DBNAME,
    Config\MYSQL_USERNAME,
    Config\MYSQL_PASSWORD
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Crear cj_migrations si no existe
$pdo->exec("CREATE TABLE IF NOT EXISTS cj_migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

switch ($action) {
    case 'migrate':
        $executed = $pdo->query("SELECT migration FROM cj_migrations")->fetchAll(PDO::FETCH_COLUMN) ?: [];

        foreach ($files as $file) {
            $migrationFile = basename($file, '.php');

            if (in_array($migrationFile, $executed)) {
                echo "🔁 $migrationFile ya ejecutada, saltando...\n";
                continue;
            }

            require_once $file;

            // Detectar nombre de clase definida en el archivo
            $tokens = token_get_all(file_get_contents($file));
            $className = null;
            for ($i = 0; $i < count($tokens); $i++) {
                if ($tokens[$i][0] === T_CLASS && $tokens[$i + 2][0] === T_STRING) {
                    $className = $tokens[$i + 2][1];
                    break;
                }
            }

            if ($className && class_exists($className)) {
                echo "⏫ Ejecutando migración: $className\n";
                $migration = new $className();
                $migration->up();

                $stmt = $pdo->prepare("INSERT INTO cj_migrations (migration, created_at, updated_at) VALUES (?, NOW(), NOW())");
                $stmt->execute([$migrationFile]);
            } else {
                echo "❌ No se pudo detectar la clase dentro de $migrationFile\n";
            }
        }
        break;

    case 'rollback':
        $last = $pdo->query("SELECT migration FROM cj_migrations ORDER BY id DESC LIMIT 1")->fetchColumn();

        if ($last) {
            echo "⏬ Revirtiendo migración: $last...\n";
            $file = $migrationsPath . $last . '.php';

            if (!file_exists($file)) {
                echo "❌ Archivo no encontrado: $file\n";
                break;
            }

            require_once $file;

            // Detectar nombre de clase
            $tokens = token_get_all(file_get_contents($file));
            $className = null;
            for ($i = 0; $i < count($tokens); $i++) {
                if ($tokens[$i][0] === T_CLASS && $tokens[$i + 2][0] === T_STRING) {
                    $className = $tokens[$i + 2][1];
                    break;
                }
            }

            if ($className && class_exists($className)) {
                $migration = new $className();
                $migration->down();
                $pdo->prepare("DELETE FROM cj_migrations WHERE migration = ?")->execute([$last]);
                echo "✅ Migración revertida: $className\n";
            } else {
                echo "❌ No se pudo detectar la clase en la migración $last\n";
            }
        } else {
            echo "⚠️  No hay migraciones para revertir.\n";
        }
        break;

    default:
        echo "Uso: php migrate.php [migrate|rollback]\n";
        break;
}
