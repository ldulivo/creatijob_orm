<?php

/**
 * File: Header.php
 * Created at: 2023-07-10 16:00:00
 * Author: LDulivo
 * -----
 * Description: 
 * This file defines the Header class which is responsible for setting HTTP headers.
 * It configures headers for access control, allowed methods, and content type.
 * 
 * Details:
 * 1. The constructor sets the URI based on the HTTP/HTTPS protocol defined in the configuration.
 * 2. The http method determines the protocol to be used (http or https).
 * 3. The accessControl method sets headers for:
 *    - Access-Control-Allow-Origin
 *    - Access-Control-Allow-Headers
 *    - Access-Control-Allow-Methods
 *    - Content-type
 * -----
 */

namespace App\Core;

use Config;
use App\Core\Exceptions\SecurityException;

class Header
{
  private string $_developmentMode;
  private string $_origin = '';
  private array $_allowedOrigins;
  private bool $_isSecureOrigin;

  public function __construct()
  {
    $this->_developmentMode = Config\DEVELOPMENT_MODE;
    $this->_origin = self::origin();
    $this->_allowedOrigins = $this->allowedOrigins($this->_origin);
    $this->_isSecureOrigin = self::secureOrigin($this->_origin);
  }

  private static function origin()
  {
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    return $_SERVER['HTTP_ORIGIN']
      ?? ($referer ? parse_url($referer, PHP_URL_SCHEME) . '://' . parse_url($referer, PHP_URL_HOST) : '');
  }

  private static function secureOrigin($origin = '')
  {
    return str_starts_with($origin, 'https://');
  }

  private function allowedOrigins($origin)
  {
    $allowedOrigins = Config\ALLOWED_ORIGINS;

    if (!$this->_developmentMode)
      return $allowedOrigins;

    if (
      str_starts_with($this->_origin, 'http://localhost') ||
      str_starts_with($this->_origin, 'http://127.') ||
      str_starts_with($this->_origin, 'http://[::1]')
    ) {
      $allowedOrigins[] = $origin;
    }

    return $allowedOrigins;
  }

  private function setCorsHeaders()
  {
    if (!$this->_isSecureOrigin && !$this->_developmentMode) {
      throw new SecurityException();
    }
  
    if (in_array($this->_origin, $this->_allowedOrigins)) {
      header("Access-Control-Allow-Origin: $this->_origin");
  
      if (Config\ALLOW_CREDENTIALS)
        header('Access-Control-Allow-Credentials: true');
    }
  
    header("Access-Control-Allow-Methods: " . Config\ALLOWED_METHODS);
    header("Access-Control-Allow-Headers: " . Config\ALLOWED_HEADERS);
    header("Content-type: application/json; charset=utf-8");
  
    if (Config\MAX_AGE > 0)
      header("Access-Control-Max-Age: " . Config\MAX_AGE);
  }

  public function accessControl()
  {

    $this->setCorsHeaders();

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
      http_response_code(204); // No Content
      exit;
    }
  }
}
