<?php

/**
 * File: auth.php
 * Created at: 2023-07-10 16:00:00
 * Author: Leonardo A. D'Ulivo
 * -----
 * Description:
 * This file defines the routes for authenticating users in the API.
 * It includes a route for user login, where the username and password are verified against stored user data,
 * and a JWT token is generated if the credentials are valid.
 * 
 * Details:
 * 1. The POST route `/api/auth/login` is used to authenticate a user. 
 *    It checks if the provided username and password match an existing user in the `users.json` file. 
 *    If the credentials are valid, a JWT token is generated and returned in the response.
 * -----
 */

use App\Core\App;
use App\Auth\Jwt;

App::post('/api/auth/login', function ($req, $res) {
  $username = $req->body['username'];
  $password = $req->body['password'];
  
  $users = json_decode(file_get_contents(API_PATH . '/users.json'), true);

  foreach ($users as $user) {
    if ($user['username'] === $username && password_verify($password, $user['password'])) {
      $token = new Jwt();
    }
  }

  isset($token)
  ? $res::json([
      'username' => $username,
      'password' => $password,
      'token' => $token->Get($username)
    ], 200)
  : $res::json([
    'message' => 'This username or password does not exist!'
  ], 200);
});
