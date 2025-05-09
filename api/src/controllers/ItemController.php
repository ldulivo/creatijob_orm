<?php

namespace Src\Controllers;

use Src\Models\ItemModel;

class ItemController
{
    public static function get($req, $res)
    {
        $query = new ItemModel();

        $page = $req->queryParameters['page'] ?? 1;
        $limit = $req->queryParameters['limit'] ?? 10;
        $offset = ($page - 1) * $limit;

        $items = $query->select('*')
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
    }

    public static function getById($req, $res)
    {
        $id = $req->params['id'];
        $query = new ItemModel();
        $item = $query->select('*')
            ->where('id', '=', $id)
            ->get();

        if ($item) {
            $res::json(['item' => $item], 200);
        } else {
            $res::json(['message' => 'Item not found'], 404);
        }
    }

    public static function create($req, $res)
    {
        $name = $req->body['name'];
        $description = $req->body['description'];
        $price = $req->body['price'];
        $quantity = $req->body['quantity'];

        $query = new ItemModel();
        $itemId = $query->insert([
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'quantity' => $quantity
        ]);

        if ($itemId) {
            $res::json([
                'id' => $itemId,
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'quantity' => $quantity
            ], 201);
        } else {
            $res::json(['message' => 'Failed to create item'], 400);
        }
    }

    public static function update($req, $res)
    {
        $id = $req->params['id'];
        $name = $req->body['name'];
        $description = $req->body['description'];
        $price = $req->body['price'];
        $quantity = $req->body['quantity'];

        $query = new ItemModel();
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
    }

    public static function delete($req, $res)
    {
        $id = $req->params['id'];
        $query = new ItemModel();
        $query->where('id', '=', $id);
        
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
    }
}
