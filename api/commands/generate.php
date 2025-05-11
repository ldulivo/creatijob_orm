<?php

function generate_migration(): void
{
    if (_ARGC < 2) {
        echo "Uso: php creatijob.php make:migration NombreDeLaMigracion\n";
        exit;
    }

    $className = _ARG;

    // Validar nombre de clase
    if (!preg_match('/^[A-Z][A-Za-z0-9_]*$/', $className)) {
        echo "❌ Nombre de clase inválido. Debe comenzar en mayúscula y no tener espacios.\n";
        exit;
    }

    // Convertir nombre de archivo a snake_case
    $filename = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className));
    $timestamp = date('Y_m_d_His');
    $fullFilename = "$timestamp" . "_$filename.php";
    $targetPath = ROOT_PATH . "/migrations/$fullFilename";

    // Detectar si es del tipo AddXXXToYYYTable
    $isAlter = false;
    $tableName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className));
    $columnName = 'column_name';

    if (preg_match('/^Add([A-Za-z0-9]+)To([A-Za-z0-9]+)Table$/', $className, $match)) {
        $isAlter = true;
        $columnName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $match[1]));
        $tableName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $match[2]));
    } elseif (preg_match('/^Create([A-Za-z0-9]+)Table$/', $className, $match)) {
        $tableName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $match[1]));
    }

    // Generar plantilla según el tipo
    if ($isAlter) {
        $template = <<<PHP
<?php

use App\Db\Migration;

class $className extends Migration
{
    public function up()
    {
        \$this->alter('$tableName', function (\$table) {
            \$table->string('$columnName')->nullable();
        });
    }

    public function down()
    {
        \$this->dropColumn('$tableName', '$columnName');
    }
}
PHP;
    } else {
        $template = <<<PHP
<?php

use App\Db\Migration;

class $className extends Migration
{
    public function up()
    {
        \$this->create('$tableName', function (\$table) {
            \$table->id();
            \$table->string('name');
            \$table->timestamps();
        });
    }

    public function down()
    {
        \$this->drop('$tableName');
    }
}
PHP;
    }

    if (!is_dir(ROOT_PATH . '/migrations')) {
        mkdir(ROOT_PATH . '/migrations', 0755, true);
    }

    file_put_contents($targetPath, $template);
    echo "✅ Migración creada: migrations/$fullFilename\n";
}
