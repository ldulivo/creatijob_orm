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
 * 2. The PUT route `/api/users/:username` is used to update an existing user. 
 *    It checks if the user exists in the `users.json` file, hashes the new password using bcrypt, 
 *    and updates the user in the `users.json` file.
 * 3. The DELETE route `/api/users/:username` is used to delete an existing user. 
 *    It checks if the user exists in the `users.json` file, and deletes the user from the `users.json` file.
 * -----
 */

use App\Core\App;
use App\Auth\UserJson;
use App\Core\Context;
use App\Middleware\AuthMiddleware;
use App\Middleware\PassFollowingRoles;

App::post('/api/users/create', function ($req, $res) {
  $dataUser['name'] = $req->body['name'];
  $dataUser['username'] = $req->body['username'];
  $dataUser['email'] = $req->body['email'];
  $dataUser['role'] = $req->body['role'];
  $dataUser['password'] = $req->body['password'];
  $user = UserJson::AddUser($dataUser);

  if (!$user) {
    return $res::json(
      ["message" => "User already exists"],
      400
    );
  }

  return $res::json(
    ["message" => "User registered successfully"],
    200
  );
  
});

App::put('/api/users/:username', function ($req, $res) {
  $dataUser['username'] = $req->params['username'];
  $dataUser['name'] = $req->body['name'];
  $dataUser['email'] = $req->body['email'];
  $dataUser['role'] = $req->body['role'];
  $dataUser['password'] = $req->body['password'];

  // context returned from the middleware
  $auth = Context::get('auth');

  // if username is equal to the auth username or auth is role admin, update the user
  if ($auth['username'] !== $dataUser['username'] && $auth['role'] !== 'admin') {
    return $res::json(
      ["message" => "You are not authorized to update this user"],
      403
    );
  }

  $user = UserJson::UpdateUser($dataUser);

  if (!$user) {
    return $res::json(
      ["message" => "User not found"],
      400
    );
  }

  return $res::json(
    ["message" => "User updated successfully", 'user' => UserJson::FindUser($dataUser['username']), 'auth' => $auth],
    200
  );
}, function($req, $res) {
  // return AuthMiddleware::Handle($req, $res);
  $auth = AuthMiddleware::Handle($req, $res);
  if (!$auth) {
    return false;
  }
  return PassFollowingRoles::Handle($req, $res, ['admin']);
});

App::del('/api/users/:username', function ($req, $res) {
  $username = $req->params['username'];
  $user = UserJson::DeleteUser($username);

  if (!$user) {
    return $res::json(
      ["message" => "User not found"],
      400
    );
  }

  return $res::json(
    ["message" => "User deleted successfully"],
    200
  );
});

