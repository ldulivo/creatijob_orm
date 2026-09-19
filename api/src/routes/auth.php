<?php

/**
 * File: auth.php
 * Description:
 * Rutas de autenticación de la API.
 * Soporta primer login con 'admin' / 'admin123', autenticación vía base de datos,
 * emisión y verificación de tokens JWT (/api/auth/me) y cambio de contraseña.
 */

use App\Core\App;
use App\Core\Context;
use App\Auth\Jwt;
use Src\Models\UserModel;
use App\Middleware\AuthMiddleware;

/**
 * POST /api/auth/login
 * Inicia sesión con usuario y contraseña
 */
App::post('/api/auth/login', function ($req, $res) {
  $username = trim($req->body['username'] ?? '');
  $password = trim($req->body['password'] ?? '');

  if (empty($username) || empty($password)) {
    return $res::json([
      'success' => false,
      'message' => 'Usuario y contraseña son requeridos.'
    ], 400);
  }

  $user = UserModel::findByUsername($username);

  // Caso 1: El usuario existe en la base de datos
  if ($user) {
    if (UserModel::verifyCredentials($username, $password)) {
      if (($user['status'] ?? 'active') === 'inactive') {
        return $res::json([
          'success' => false,
          'message' => 'Tu cuenta se encuentra inactiva. Por favor, contacta con el administrador.'
        ], 403);
      }

      $jwt = new Jwt();
      $role = $user['role'] ?? 'admin';
      $userId = $user['id'] ?? null;
      $token = $jwt->Get($username, $role, $userId);

      return $res::json([
        'success' => true,
        'username' => $username,
        'role' => $role,
        'token' => $token,
        'message' => 'Autenticación exitosa.'
      ], 200);
    }

    return $res::json([
      'success' => false,
      'message' => 'Usuario o contraseña incorrectos.'
    ], 401);
  }

  // Caso 2: El usuario 'admin' no existe en la base de datos (primer acceso por defecto)
  if ($username === 'admin' && $password === 'admin123') {
    $jwt = new Jwt();
    $role = 'admin';
    $token = $jwt->Get('admin', $role, 1);

    return $res::json([
      'success' => true,
      'username' => 'admin',
      'role' => $role,
      'token' => $token,
      'message' => 'Autenticación exitosa (primer acceso como administrador).'
    ], 200);
  }

  return $res::json([
    'success' => false,
    'message' => 'Usuario o contraseña incorrectos.'
  ], 401);
});

/**
 * GET /api/auth/me
 * Obtiene los datos del usuario autenticado a través del token JWT
 */
App::get('/api/auth/me', function ($req, $res) {
  $auth = Context::get('auth');
  $username = $auth['username'] ?? '';
  $user = UserModel::findByUsername($username);

  if ($user) {
    if (($user['status'] ?? 'active') === 'inactive') {
      return $res::json([
        'success' => false,
        'message' => 'Usuario no encontrado o inactivo.'
      ], 403);
    }
    unset($user['password']);
    return $res::json([
      'success' => true,
      'user' => $user
    ], 200);
  }

  // Si es el admin por defecto que aún no ha sido guardado en la base de datos
  if ($username === 'admin') {
    return $res::json([
      'success' => true,
      'user' => [
        'id' => 1,
        'name' => 'Administrador',
        'username' => 'admin',
        'email' => 'admin@localhost',
        'role' => 'admin',
        'status' => 'active'
      ]
    ], 200);
  }

  return $res::json([
    'success' => false,
    'message' => 'Usuario no encontrado.'
  ], 404);
}, function ($req, $res) {
  return AuthMiddleware::Handle($req, $res);
});

/**
 * POST /api/auth/logout
 * Cierre de sesión
 */
App::post('/api/auth/logout', function ($req, $res) {
  return $res::json([
    'success' => true,
    'message' => 'Sesión cerrada exitosamente.'
  ], 200);
});

/**
 * POST /api/auth/change-password
 * Permite al usuario autenticado cambiar su contraseña
 */
App::post('/api/auth/change-password', function ($req, $res) {
  $auth = Context::get('auth');
  $username = $auth['username'] ?? '';

  if (empty($username)) {
    return $res::json([
      'success' => false,
      'message' => 'No autorizado. Sesión no válida.'
    ], 401);
  }

  $currentPassword = trim($req->body['current_password'] ?? '');
  $newPassword = trim($req->body['new_password'] ?? '');

  if (empty($currentPassword) || empty($newPassword)) {
    return $res::json([
      'success' => false,
      'message' => 'La contraseña actual y la nueva contraseña son obligatorias.'
    ], 400);
  }

  if (strlen($newPassword) < 6) {
    return $res::json([
      'success' => false,
      'message' => 'La nueva contraseña debe tener al menos 6 caracteres.'
    ], 400);
  }

  $user = UserModel::findByUsername($username);

  if ($user) {
    if (!UserModel::comparePassword($username, $currentPassword)) {
      return $res::json([
        'success' => false,
        'message' => 'La contraseña actual no es correcta.'
      ], 400);
    }

    $updated = UserModel::updatePassword($username, $newPassword);
    if (!$updated) {
      return $res::json([
        'success' => false,
        'message' => 'No se pudo actualizar la contraseña.'
      ], 500);
    }
  } elseif ($username === 'admin') {
    // Si aún no existía en la base de datos y estaba usando la clave por defecto
    if ($currentPassword !== 'admin123') {
      return $res::json([
        'success' => false,
        'message' => 'La contraseña actual no es correcta.'
      ], 400);
    }

    // Se crea el admin en la base de datos con la nueva contraseña
    $created = UserModel::createUser([
      'name' => 'Administrador',
      'username' => 'admin',
      'email' => 'admin@localhost',
      'role' => 'admin',
      'status' => 'active',
      'password' => $newPassword
    ]);

    if (!$created) {
      return $res::json([
        'success' => false,
        'message' => 'No se pudo registrar la nueva contraseña en la base de datos.'
      ], 500);
    }
  } else {
    return $res::json([
      'success' => false,
      'message' => 'Usuario no encontrado.'
    ], 404);
  }

  return $res::json([
    'success' => true,
    'message' => 'Contraseña actualizada exitosamente.'
  ], 200);
}, function ($req, $res) {
  return AuthMiddleware::Handle($req, $res);
});
