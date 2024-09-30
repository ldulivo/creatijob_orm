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

class Header
{
  private string $_URI;

  public function __construct()
  {
    $this->_URI = self::http();
  }

  private static function http()
  {
    Config\DEBUGMODE === true
      ? $_http = 'http'
      : $_http = 'https';

    return $_http;
  }

  public function accessControl()
  {
    header("Access-Control-Allow-Origin: $this->_URI");
    header('Access-Control-Allow-Headers: content-type');
    header('Access-Control-Allow-Methods: OPTIONS,GET,PUT,POST,DELETE');
    header('Content-type: application/json; charset=utf-8');
  }
}