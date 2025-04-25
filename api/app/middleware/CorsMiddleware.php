<?php

namespace App\Middleware;

class CorsMiddleware
{
  public static function Handle($req, $res)
  {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Authorization, Content-Type");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header('Access-Control-Max-Age', '86400');
  }
}
?>