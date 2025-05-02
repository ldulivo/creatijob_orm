<?php

use App\Core\App;
use App\Db\QueryBuilder;

$rute = '/api/dbq_items';

/**
 * Query Builder Example
 */
// create item using query builder
App::post($rute, function ($req, $res) {
  $query = new QueryBuilder();
  $data = [
    'name' => $req->body['name'],
    'description' => $req->body['description'],
    'price' => $req->body['price'],
    'quantity' => $req->body['quantity']
  ];
  $item = $query->table('items')->insert($data);

  if ($item) {
    $res::json([
        'id' => $item,
        'name' => $data['name'],
        'description' => $data['description'],
        'price' => $data['price'],
        'quantity' => $data['quantity'],
        'count' => $query->count()
    ], 201);
  } else {
    $res::json(['message' => 'Failed to create item'], 400);
  }

});

// get all items using query builder
App::get($rute, function ($req, $res) {
  $query = new QueryBuilder();

  $page = $req->queryParameters['page'] ?? 1;
  $limit = $req->queryParameters['limit'] ?? 10;
  $offset = ($page - 1) * $limit;

  $items = $query->table('items')
    ->select('*')
    ->limit($limit)
    ->offset($offset)
    ->get();
  
  $totalItems = $query->count();
  $totalPages = ceil($totalItems / $limit);

  $res::json([
      'items' => $items,
      'totalItems' => $totalItems,
      'totalPages' => $totalPages,
      'currentPage' => $page
  ], 200);
});

// get item by id using query builder
App::get("$rute/:id", function ($req, $res) { 
  $id = $req->params['id'];
  $queryBuilder = new QueryBuilder();
  $item = $queryBuilder->table('items')
    ->select('*')
    ->where('id', '=', $id)
    ->get();

  if ($item) {
    $res::json(['item' => $item], 200);
  } else {
    $res::json(['message' => 'Item not found'], 404);
  }
});

// update item using query builder
App::put("$rute/:id", function ($req, $res) {
  $id = $req->params['id'];
  $name = $req->body['name'];
  $description = $req->body['description'];
  $price = $req->body['price'];
  $quantity = $req->body['quantity'];

  $query = new QueryBuilder();
  $item =$query->table('items')
    ->where('id', '=', $id)
    ->update([
      'name' => $name,
      'description' => $description,
      'price' => $price,
      'quantity' => $quantity
    ]);

    if ($item === false) {
      $res::json(['message' => 'Record not found'], 400);
      return;
    }

    if ($item === null) {
      $res::json(['message' => 'No changes in the registry'], 200);
      return;
    }
    
    $res::json([
        'id' => $id,
        'name' => $name,
        'description' => $description,
        'price' => $price,
        'quantity' => $quantity
    ], 200);
});

// delete item using query builder
App::del("$rute/:id", function ($req, $res) {
  $id = $req->params['id'];
  $query = new QueryBuilder();
  $query->table('items')
    ->where('id', '=', $id);
  
  if (!$query->get() > 0) {
    $res::json(['message' => 'Item not found'], 404);
    return;
  }

  $query->delete();

  if ($query->get() > 0) {
      $res::json(['message' => 'Item deleted'], 200);
  } else {
      $res::json(['message' => 'Item not found'], 404);
  }
});

?>