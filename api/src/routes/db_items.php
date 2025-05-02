<?php


use App\Core\App;
use App\Db\Database;

use App\Middleware\AuthMiddleware;


App::post('/api/db_items', function ($req, $res) {
  $name = $req->body['name'];
  $description = $req->body['description'];
  $price = $req->body['price'];
  $quantity = $req->body['quantity'];

  $sql = "INSERT INTO items (name, description, price, quantity) VALUES (?, ?, ?, ?)";
  $itemId = Database::insert($sql, [$name, $description, $price, $quantity]);

  $res::json([
      'id' => $itemId,
      'name' => $name,
      'description' => $description,
      'price' => $price,
      'quantity' => $quantity
  ], 201);
});


App::get('/api/db_items', function ($req, $res) {
  $sql = "SELECT * FROM items";
  $items = Database::fetchAll($sql);

  $res::json([
      'items' => $items
  ], 200);
}, function($req, $res) {
  $auth = AuthMiddleware::Handle($req, $res);

  if (!$auth) {
    return false;
  }
  return true;
});


App::get('/api/db_items/:id', function ($req, $res) {
  $id = $req->params['id'];
  $sql = "SELECT * FROM items WHERE id = ?";
  $item = Database::fetch($sql, [$id]);

  if ($item) {
      $res::json(['item' => $item], 200);
  } else {
      $res::json(['message' => 'Item not found'], 404);
  }
});


App::put('/api/db_items/:id', function ($req, $res) {
  $id = $req->params['id'];
  $name = $req->body['name'];
  $description = $req->body['description'];
  $price = $req->body['price'];
  $quantity = $req->body['quantity'];

  $sql = "UPDATE items SET name = ?, description = ?, price = ?, quantity = ? WHERE id = ?";
  $affectedRows = Database::execute($sql, [$name, $description, $price, $quantity, $id]);

  if ($affectedRows > 0) {
      $res::json([
          'id' => $id,
          'name' => $name,
          'description' => $description,
          'price' => $price,
          'quantity' => $quantity
      ], 200);
  } else {
      $res::json(['message' => 'Failed to update item'], 400);
  }
});


App::del('/api/db_items/:id', function ($req, $res) {
  $id = $req->params['id'];
  $sql = "DELETE FROM items WHERE id = ?";
  $affectedRows = Database::execute($sql, [$id]);

  if ($affectedRows > 0) {
      $res::json(['message' => 'Item deleted'], 200);
  } else {
      $res::json(['message' => 'Item not found'], 404);
  }
});


?>