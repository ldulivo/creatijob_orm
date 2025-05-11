<?php

function generate_resource(): void
{
    if (_ARGC < 2) {
        echo "Usage: php creatijob.php make:resource ResourceName\n";
        exit;
    }

    $name = _ARG;
    $classBase = ucfirst($name);                     // Roles
    $lowerBase = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $classBase)); // roles

    $modelName = $classBase . 'Model';
    $controllerName = $classBase . 'Controller';
    $routeFile = ROOT_PATH . "/src/routes/{$lowerBase}.php";
    $modelFile = ROOT_PATH . "/src/models/{$modelName}.php";
    $controllerFile = ROOT_PATH . "/src/controllers/{$controllerName}.php";

    // Modelo
    $modelContent = <<<PHP
<?php

namespace Src\Models;

use App\Db\Model;

class $modelName extends Model
{
    protected \$table = '$lowerBase';
}
PHP;

    // Controlador
    $controllerContent = <<<PHP
<?php

namespace Src\Controllers;

use Src\Models\\$modelName;

class $controllerName
{
    public static function get(\$req, \$res)
    {
        \$query = new $modelName();

        \$page = \$req->queryParameters['page'] ?? 1;
        \$limit = \$req->queryParameters['limit'] ?? 10;
        \$offset = (\$page - 1) * \$limit;

        \$items = \$query->select('*')
            ->limit(\$limit)
            ->offset(\$offset)
            ->get();

        \$totalItems = \$query->count();
        \$totalPages = ceil(\$totalItems / \$limit);

        \$res::json([
            'items' => \$items,
            'totalItems' => \$totalItems,
            'totalPages' => \$totalPages,
            'currentPage' => \$page
        ], 200);
    }

    public static function getById(\$req, \$res)
    {
        \$id = \$req->params['id'];
        \$query = new $modelName();
        \$item = \$query->select('*')
            ->where('id', '=', \$id)
            ->get();

        if (\$item) {
            \$res::json(['item' => \$item], 200);
        } else {
            \$res::json(['message' => '$classBase not found'], 404);
        }
    }

    public static function create(\$req, \$res)
    {
        \$data = \$req->body;
        \$query = new $modelName();
        \$id = \$query->insert(\$data);

        if (\$id) {
            \$res::json(array_merge(['id' => \$id], \$data), 201);
        } else {
            \$res::json(['message' => 'Failed to create $lowerBase'], 400);
        }
    }

    public static function update(\$req, \$res)
    {
        \$id = \$req->params['id'];
        \$data = \$req->body;

        \$query = new $modelName();
        \$updated = \$query->table('$lowerBase')
            ->where('id', '=', \$id)
            ->update(\$data);

        if (\$updated === false) {
            \$res::json(['message' => 'Record not found'], 400);
        } elseif (\$updated === null) {
            \$res::json(['message' => 'No changes in the registry'], 200);
        } else {
            \$res::json(array_merge(['id' => \$id], \$data), 200);
        }
    }

    public static function delete(\$req, \$res)
    {
        \$id = \$req->params['id'];
        \$query = new $modelName();
        \$query->where('id', '=', \$id);

        if (!\$query->get()) {
            \$res::json(['message' => '$classBase not found'], 404);
            return;
        }

        \$query->delete();
        \$res::json(['message' => '$classBase deleted'], 200);
    }
}
PHP;

    // Rutas
    $routeContent = <<<PHP
<?php

use App\Core\App;
use Src\Controllers\\$controllerName;

\$rute = '/api/$lowerBase';

App::get(\$rute, [$controllerName::class, 'get']);
App::get("\$rute/:id", [$controllerName::class, 'getById']);
App::post(\$rute, [$controllerName::class, 'create']);
App::put("\$rute/:id", [$controllerName::class, 'update']);
App::del("\$rute/:id", [$controllerName::class, 'delete']);
PHP;

    file_put_contents($modelFile, $modelContent);
    file_put_contents($controllerFile, $controllerContent);
    file_put_contents($routeFile, $routeContent);

    echo "✅ Resource created: \n- $modelFile\n- $controllerFile\n- $routeFile\n";
}
