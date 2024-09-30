<?php

/**
 * File: items.php
 * Created at: 2023-07-10 16:00:00
 * Author: LDulivo
 * -----
 * Description: 
 * This file defines the routes for the items API.
 * It defines the routes for creating, retrieving, updating, and deleting items.
 * 
 * Details:
 * 1. The route for creating a new item is defined with a POST request.
 * 2. The route for retrieving all items is defined with a GET request.
 * 3. The route for retrieving a specific item is defined with a GET request.
 * 4. The route for updating an item is defined with a PUT request.
 * 5. The route for deleting an item is defined with a DELETE request.
 * -----
 * Example:
 * In this example, we use only routes without the integration of a database. We simulate the properties of an item in a food store.
 */

use App\Core\App;

App::post('/api/items', function ($req, $res) {
  $data = [
    'name' => $req->body['name'],
    'description' => $req->body['description'],
    'price' => $req->body['price'],
    'quantity' => $req->body['quantity'],
  ];

  $res::json($data, 200);
});

App::get('/api/items', function ($req, $res) {
  $data = [
    'items' => [
      [
        'id' => 1,
        'name' => 'Apple',
        'description' => 'A fresh apple.',
        'price' => 0.5,
        'quantity' => 50,
      ],
      [
        'id' => 2,
        'name' => 'Banana',
        'description' => 'A ripe banana.',
        'price' => 0.3,
        'quantity' => 100,
      ],
    ],
  ];

  $res::json($data, 200);
});

App::get('/api/items/:id', function ($req, $res) {
  $data = [
    'item' => [
      'id' => $req->params['id'],
      'name' => 'Apple',
      'description' => 'A fresh apple.',
      'price' => 0.5,
      'quantity' => 50,
    ],
  ];

  $res::json($data, 200);
});

App::put('/api/items/:id', function ($req, $res) {
  $data = [
    'item' => [
      'id' => $req->params['id'],
      'name' => $req->body['name'],
      'description' => $req->body['description'],
      'price' => $req->body['price'],
      'quantity' => $req->body['quantity'],
    ],
  ];

  $res::json($data, 200);
});

App::del('/api/items/:id', function ($req, $res) {
  $data = [
    'item' => [
      'id' => $req->params['id'],
      'name' => 'Apple',
      'description' => 'A fresh apple.',
      'price' => 0.5,
      'quantity' => 50,
    ],
  ];

  $res::json($data, 200);
});
?>