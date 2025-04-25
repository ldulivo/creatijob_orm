<?php

namespace App\Middleware;

use App\Auth\Jwt;
use App\Core\Context;

/**
 * File: AuthMiddleware.php
 * Created at: 2025-04-22 16:00:00
 * Author: Leonardo A. D'Ulivo
 * -----
 * Description:
 * This file defines the AuthMiddleware class, which is responsible for handling authentication
 * in the API. It verifies the JWT token in the request headers and sets the username in the request object.
 * -----
 */

class AuthMiddleware
{
  public static function Handle($req, $res)
  {
    $xtoken = $req->getHeader(['Authorization', 'xtoken', 'x-token']);

    if (!$xtoken) {
      return $res::json([
        'message' => 'No token provided.'
      ], 401);
    }

    // If it comes in 'Bearer xxx' format, extract only the token
    if (str_starts_with($xtoken, 'Bearer ')) {
      $xtoken = substr($xtoken, 7);
    }

    $token = new Jwt();
    $payload = $token->Decode($xtoken);

    if (!$payload) {
      $res::json([
        'message' => 'Expired Token!'
      ], 400);
      return false;
    }

    Context::set('auth', $payload);
    return $payload;
  }
}
