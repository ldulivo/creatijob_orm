<?php

/**
 * File: users.php
 * Created at: 2023-07-10 16:00:00
 * Author: Leonardo A. D'Ulivo
 * -----
 * Description:
 * This file defines the routes for managing users in the API.
 * It includes routes for creating a new user and for handling user-specific requests with JWT authentication.
 * 
 * Details:
 * 1. The POST route `/api/users/create` is used to create a new user. 
 *    It checks if the user already exists in the `users.json` file, hashes the password using bcrypt, 
 *    and stores the new user in the `users.json` file.
 * 2. The POST route `/api/users` retrieves user information and validates the JWT token provided in the request header.
 *    The middleware function validates the token and checks if it matches the username provided in the request body.
 * -----
 */

use App\Core\App;
use App\Auth\Jwt;

App::post('/api/users/create', function ($req, $res) {
  $username = $req->body['username'];
  $password = password_hash($req->body['password'], PASSWORD_BCRYPT);
  $users = json_decode(file_get_contents(API_PATH . '/users.json'), true);

  foreach ($users as $user) {
    if ($user['username'] === $username) {
      return $res::json(
        ["message" => "User already exists"],
        400
      );
    }
  }
  
  $users[] = ['username' => $username, 'password' => $password];
  file_put_contents(API_PATH . '/users.json', json_encode($users));

  return $res::json(
    ["message" => "User registered successfully"],
    200
  );
  
});

App::post('/api/users', function($req, $res) {
  $username = $req->body['username'];
  $xtoken = $req->headers['xtoken'];

  $res::json([
    'username' => $username,
    'xtoken' => $xtoken,
  ], 200);
}, function($req, $res) {
  $username = $req->body['username'];
  $xtoken = $req->headers['xtoken'];

  $token = new Jwt();
  $isValidToken = $token->Compare($xtoken, $username);

  if (!$isValidToken) {
    $res::json([
      'message' => 'Expired Token!'
    ], 400);
    return false;
  }
});

