<?php

/**
 * File: ReadRouteFiles.php
 * Created at: 2023-07-10 16:00:00
 * Author: LDulivo
 * -----
 * Description: 
 * This file defines the ReadRouteFiles class which is responsible for loading route files.
 * It dynamically includes all PHP files located in the routes directory.
 * 
 * Details:
 * 1. The getFiles method uses glob to find all PHP files in the specified routes directory.
 * 2. Includes each found route file to make its contents available to the application.
 * -----
 */

namespace App\Core;

class ReadRouteFiles
{
  public static function getFiles()
  {
    foreach (glob( WEB_ROOT . '/api/src/routes/*.php') as $filename )
    {
      require_once($filename);
    }
  }
}