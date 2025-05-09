<?php

use App\Core\App;
use Src\Controllers\ItemController;

$rute = '/api/db_model_items';

App::get($rute, [ItemController::class, 'get']);
App::get("$rute/:id", [ItemController::class, 'getById']);
App::post($rute, [ItemController::class, 'create']);
App::put("$rute/:id", [ItemController::class, 'update']);
App::del("$rute/:id", [ItemController::class, 'delete']);
